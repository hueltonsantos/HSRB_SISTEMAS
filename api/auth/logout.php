<?php
/**
 * Endpoint de Logout
 * POST /api/auth/logout
 * 
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "message": "Logout realizado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação
$user = requireAuth();

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Registrar log de logout
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, ip, user_agent, data_hora) 
            VALUES (?, ?, 'logout', 'auth', 'Logout via API mobile', ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    // Nota: Em uma implementação mais robusta, você poderia:
    // 1. Adicionar o token a uma blacklist no banco
    // 2. Invalidar refresh tokens associados
    // 3. Limpar sessões ativas
    
    ApiResponse::success(null, 'Logout realizado com sucesso');
    
} catch (PDOException $e) {
    error_log("Erro no logout API: " . $e->getMessage());
    ApiResponse::serverError('Erro ao processar logout');
}
