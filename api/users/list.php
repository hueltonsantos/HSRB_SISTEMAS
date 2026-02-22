<?php
/**
 * Endpoint de Listagem de Usuários
 * GET /api/users/list
 * 
 * Query params: ?page=1&limit=20&search=nome&status=1&perfil_id=1
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "items": [...], "total": 150, "page": 1, "pages": 8 } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'user_manage');

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Parâmetros de paginação
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    // Parâmetros de filtro
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? (int)$_GET['status'] : null;
    $perfilId = isset($_GET['perfil_id']) ? (int)$_GET['perfil_id'] : null;
    
    // Construir query
    $where = [];
    $params = [];
    
    // Filtro de busca
    if ($search) {
        $where[] = '(u.nome LIKE ? OR u.email LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Filtro de status
    if ($status !== null) {
        $where[] = 'u.status = ?';
        $params[] = $status;
    }
    
    // Filtro de perfil
    if ($perfilId) {
        $where[] = 'u.perfil_id = ?';
        $params[] = $perfilId;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Contar total de registros
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM usuarios u $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();
    
    // Buscar usuários
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.nome,
            u.email,
            u.foto,
            u.perfil_id,
            u.clinica_id,
            u.nivel_acesso,
            u.ultimo_acesso,
            u.status,
            u.data_cadastro,
            p.nome AS perfil_nome,
            c.nome AS clinica_nome
        FROM usuarios u
        LEFT JOIN perfis p ON u.perfil_id = p.id
        LEFT JOIN clinicas_parceiras c ON u.clinica_id = c.id
        $whereClause
        ORDER BY u.nome ASC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar dados
    foreach ($usuarios as &$usuario) {
        $usuario['id'] = (int)$usuario['id'];
        $usuario['perfil_id'] = $usuario['perfil_id'] ? (int)$usuario['perfil_id'] : null;
        $usuario['clinica_id'] = $usuario['clinica_id'] ? (int)$usuario['clinica_id'] : null;
        $usuario['status'] = (int)$usuario['status'];
        
        // Remover senha do retorno
        unset($usuario['senha']);
    }
    
    // Calcular total de páginas
    $pages = ceil($total / $limit);
    
    ApiResponse::success([
        'items' => $usuarios,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao listar usuários: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar usuários');
}
