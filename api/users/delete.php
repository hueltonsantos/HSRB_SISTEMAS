<?php
/**
 * Endpoint para deletar um usuário
 * DELETE /api/users/delete?id=1
 * 
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "message": "Usuário deletado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas DELETE é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'user_manage');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do usuário não fornecido', 400);
}

$userId = (int)$_GET['id'];

// Não permitir deletar a si mesmo
if ($userId === $user['user_id']) {
    ApiResponse::error('Você não pode deletar seu próprio usuário', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se usuário existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        ApiResponse::notFound('Usuário não encontrado');
    }
    
    // Verificar se usuário tem registros vinculados (soft delete)
    $stmtCheck = $pdo->prepare("
        SELECT COUNT(*) FROM logs_sistema WHERE usuario_id = ?
    ");
    $stmtCheck->execute([$userId]);
    $totalLogs = $stmtCheck->fetchColumn();
    
    if ($totalLogs > 0) {
        // Soft delete (apenas desativar)
        $stmt = $pdo->prepare("UPDATE usuarios SET status = 0 WHERE id = ?");
        $stmt->execute([$userId]);
        $message = 'Usuário desativado com sucesso (possui registros vinculados)';
    } else {
        // Hard delete (deletar permanentemente)
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $message = 'Usuário deletado com sucesso';
    }
    
    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, ip, user_agent, data_hora) 
            VALUES (?, ?, 'excluir', 'usuarios', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $message,
            $userId,
            json_encode($usuario, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    ApiResponse::success(null, $message);
    
} catch (PDOException $e) {
    error_log("Erro ao deletar usuário: " . $e->getMessage());
    ApiResponse::serverError('Erro ao deletar usuário');
}
