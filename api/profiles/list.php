<?php
/**
 * Endpoint de Listagem de Perfis
 * GET /api/profiles/list
 * 
 * Query params: ?page=1&limit=20&search=nome&status=1
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "items": [...], "total": 10, "page": 1, "pages": 1 } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'role_manage');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? (int)$_GET['status'] : null;
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = '(nome LIKE ? OR descricao LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if ($status !== null) {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM perfis $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();
    
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nome,
            descricao,
            status
        FROM perfis
        $whereClause
        ORDER BY nome ASC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $perfis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($perfis as &$perfil) {
        $perfil['id'] = (int)$perfil['id'];
        $perfil['status'] = (int)$perfil['status'];
        
        // Buscar permissões do perfil
        $stmtPerms = $pdo->prepare("
            SELECT p.id, p.nome, p.chave
            FROM perfil_permissoes pp
            JOIN permissoes p ON pp.permissao_id = p.id
            WHERE pp.perfil_id = ?
        ");
        $stmtPerms->execute([$perfil['id']]);
        $perfil['permissoes'] = $stmtPerms->fetchAll(PDO::FETCH_ASSOC);
        
        // Contar usuários com este perfil
        $stmtUsers = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE perfil_id = ?");
        $stmtUsers->execute([$perfil['id']]);
        $perfil['total_usuarios'] = (int)$stmtUsers->fetchColumn();
    }
    
    $pages = ceil($total / $limit);
    
    ApiResponse::success([
        'items' => $perfis,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao listar perfis: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar perfis');
}
