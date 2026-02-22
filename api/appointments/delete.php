<?php
/**
 * Endpoint para cancelar (soft delete) um agendamento
 * DELETE /api/appointments/delete?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "message": "Agendamento cancelado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas DELETE é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_create');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do agendamento não fornecido', 400);
}

$appointmentId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se agendamento existe e pertence à clínica do usuário
    $clinicaFilter = '';
    $params = [$appointmentId];

    if ($user['clinica_id']) {
        $clinicaFilter = ' AND clinica_id = ?';
        $params[] = $user['clinica_id'];
    }

    $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?" . $clinicaFilter);
    $stmt->execute($params);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamento) {
        ApiResponse::notFound('Agendamento não encontrado');
    }

    // Soft delete: atualizar status para 'cancelado'
    $stmt = $pdo->prepare("UPDATE agendamentos SET status_agendamento = 'cancelado', ultima_atualizacao = NOW() WHERE id = ?");
    $stmt->execute([$appointmentId]);

    $message = 'Agendamento cancelado com sucesso';

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, ip, user_agent, data_hora)
            VALUES (?, ?, 'excluir', 'agendamentos', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $message,
            $appointmentId,
            json_encode($agendamento, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success(null, $message);

} catch (PDOException $e) {
    error_log("Erro ao cancelar agendamento: " . $e->getMessage());
    ApiResponse::serverError('Erro ao cancelar agendamento');
}
