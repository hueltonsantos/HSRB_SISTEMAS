<?php
/**
 * Endpoint para obter detalhes de um perfil
 * GET /api/profiles/get?id=1
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
    
    $perfil['id'] = (int)$perfil['id'];
    $perfil['status'] = (int)$perfil['status'];
    
    // Buscar permissões
    $stmtPerms = $pdo->prepare("
        SELECT p.id, p.nome, p.chave, p.descricao
        FROM perfil_permissoes pp
        JOIN permissoes p ON pp.permissao_id = p.id
        WHERE pp.perfil_id = ?
    ");
    $stmtPerms->execute([$perfilId]);
    $perfil['permissoes'] = $stmtPerms->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar usuários com este perfil
    $stmtUsers = $pdo->prepare("
        SELECT id, nome, email, status
        FROM usuarios
        WHERE perfil_id = ?
        ORDER BY nome ASC
    ");
    $stmtUsers->execute([$perfilId]);
    $perfil['usuarios'] = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
    
    ApiResponse::success(['profile' => $perfil]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar perfil: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar perfil');
}
