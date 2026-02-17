<?php
/**
 * Endpoint para deletar uma especialidade
 * DELETE /api/specialties/delete?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "message": "Especialidade deletada com sucesso" }
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
    ApiResponse::error('ID da especialidade não fornecido', 400);
}

$specialtyId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se especialidade existe
    $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id = ?");
    $stmt->execute([$specialtyId]);
    $especialidade = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$especialidade) {
        ApiResponse::notFound('Especialidade não encontrada');
    }

    // Verificar se possui procedimentos vinculados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM valores_procedimentos WHERE especialidade_id = ?");
    $stmt->execute([$specialtyId]);
    $totalProcedimentos = $stmt->fetchColumn();

    if ($totalProcedimentos > 0) {
        // Soft delete (apenas desativar)
        $stmt = $pdo->prepare("UPDATE especialidades SET status = 0 WHERE id = ?");
        $stmt->execute([$specialtyId]);
        $message = 'Especialidade desativada com sucesso (possui procedimentos vinculados)';
    } else {
        // Hard delete (deletar permanentemente)
        $stmt = $pdo->prepare("DELETE FROM especialidades WHERE id = ?");
        $stmt->execute([$specialtyId]);
        $message = 'Especialidade deletada com sucesso';
    }

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, ip, user_agent, data_hora)
            VALUES (?, ?, 'excluir', 'especialidades', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $message,
            $specialtyId,
            json_encode($especialidade, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success(null, $message);

} catch (PDOException $e) {
    error_log("Erro ao deletar especialidade: " . $e->getMessage());
    ApiResponse::serverError('Erro ao deletar especialidade');
}
