<?php
/**
 * Endpoint para deletar uma clínica
 * DELETE /api/clinics/delete?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "message": "Clínica deletada com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas DELETE é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'role_manage');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID da clínica não fornecido', 400);
}

$clinicaId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se clínica existe
    $stmt = $pdo->prepare("SELECT * FROM clinicas_parceiras WHERE id = ?");
    $stmt->execute([$clinicaId]);
    $clinica = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clinica) {
        ApiResponse::notFound('Clínica não encontrada');
    }

    // Verificar se clínica tem agendamentos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE clinica_id = ?");
    $stmt->execute([$clinicaId]);
    $totalAgendamentos = $stmt->fetchColumn();

    if ($totalAgendamentos > 0) {
        // Soft delete (apenas desativar)
        $stmt = $pdo->prepare("UPDATE clinicas_parceiras SET status = 0, ultima_atualizacao = NOW() WHERE id = ?");
        $stmt->execute([$clinicaId]);
        $message = 'Clínica desativada com sucesso (possui agendamentos)';
    } else {
        // Hard delete (deletar permanentemente)
        // Primeiro remover vínculos com especialidades
        $stmt = $pdo->prepare("DELETE FROM especialidades_clinicas WHERE clinica_id = ?");
        $stmt->execute([$clinicaId]);

        // Deletar clínica
        $stmt = $pdo->prepare("DELETE FROM clinicas_parceiras WHERE id = ?");
        $stmt->execute([$clinicaId]);
        $message = 'Clínica deletada com sucesso';
    }

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, ip, user_agent, data_hora)
            VALUES (?, ?, 'excluir', 'clinicas', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $message,
            $clinicaId,
            json_encode($clinica, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success(null, $message);

} catch (PDOException $e) {
    error_log("Erro ao deletar clínica: " . $e->getMessage());
    ApiResponse::serverError('Erro ao deletar clínica');
}
