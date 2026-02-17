<?php
/**
 * Endpoint para deletar um paciente
 * DELETE /api/patients/delete?id=1
 * 
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "message": "Paciente deletado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas DELETE é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_create'); // Ou criar permissão específica

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do paciente não fornecido', 400);
}

$patientId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se paciente existe
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
    $stmt->execute([$patientId]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$paciente) {
        ApiResponse::notFound('Paciente não encontrado');
    }
    
    // Verificar se paciente tem agendamentos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE paciente_id = ?");
    $stmt->execute([$patientId]);
    $totalAgendamentos = $stmt->fetchColumn();
    
    if ($totalAgendamentos > 0) {
        // Soft delete (apenas desativar)
        $stmt = $pdo->prepare("UPDATE pacientes SET status = 0 WHERE id = ?");
        $stmt->execute([$patientId]);
        $message = 'Paciente desativado com sucesso (possui agendamentos)';
    } else {
        // Hard delete (deletar permanentemente)
        $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id = ?");
        $stmt->execute([$patientId]);
        $message = 'Paciente deletado com sucesso';
    }
    
    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, ip, user_agent, data_hora) 
            VALUES (?, ?, 'excluir', 'pacientes', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $message,
            $patientId,
            json_encode($paciente, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    ApiResponse::success(null, $message);
    
} catch (PDOException $e) {
    error_log("Erro ao deletar paciente: " . $e->getMessage());
    ApiResponse::serverError('Erro ao deletar paciente');
}
