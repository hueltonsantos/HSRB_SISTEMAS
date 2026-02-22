<?php
/**
 * Endpoint para deletar um perfil
 * DELETE /api/profiles/delete?id=1
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'role_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do perfil não fornecido', 400);
}

$perfilId = (int)$_GET['id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM perfis WHERE id = ?");
    $stmt->execute([$perfilId]);
    $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$perfil) {
        ApiResponse::notFound('Perfil não encontrado');
    }
    
    // Verificar se há usuários com este perfil
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE perfil_id = ?");
    $stmtCheck->execute([$perfilId]);
    $totalUsuarios = $stmtCheck->fetchColumn();
    
    if ($totalUsuarios > 0) {
        // Soft delete
        $stmt = $pdo->prepare("UPDATE perfis SET status = 0 WHERE id = ?");
        $stmt->execute([$perfilId]);
        $message = 'Perfil desativado com sucesso (possui usuários vinculados)';
    } else {
        $pdo->beginTransaction();
        
        // Deletar permissões
        $stmt = $pdo->prepare("DELETE FROM perfil_permissoes WHERE perfil_id = ?");
        $stmt->execute([$perfilId]);
        
        // Deletar perfil
        $stmt = $pdo->prepare("DELETE FROM perfis WHERE id = ?");
        $stmt->execute([$perfilId]);
        
        $pdo->commit();
        $message = 'Perfil deletado com sucesso';
    }
    
    // Log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, ip, user_agent, data_hora) 
            VALUES (?, ?, 'excluir', 'perfis', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $message,
            $perfilId,
            json_encode($perfil, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {}
    
    ApiResponse::success(null, $message);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao deletar perfil: " . $e->getMessage());
    ApiResponse::serverError('Erro ao deletar perfil');
}
