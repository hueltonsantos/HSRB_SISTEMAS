<?php
/**
 * Endpoint de Listagem de Especialidades
 * GET /api/specialties/list
 *
 * Query params: ?page=1&limit=20&search=nome&status=1
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "items": [...], "pagination": {...} } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'role_manage');

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

    // Construir query
    $where = [];
    $params = [];

    // Filtro de busca
    if ($search) {
        $where[] = '(e.nome LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
    }

    // Filtro de status
    if ($status !== null) {
        $where[] = 'e.status = ?';
        $params[] = $status;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Contar total de registros
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM especialidades e $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    // Buscar especialidades com contagem de procedimentos
    $stmt = $pdo->prepare("
        SELECT
            e.id,
            e.nome,
            e.descricao,
            e.status,
            (SELECT COUNT(*) FROM valores_procedimentos vp WHERE vp.especialidade_id = e.id AND vp.status = 1) AS total_procedimentos
        FROM especialidades e
        $whereClause
        ORDER BY e.nome ASC
        LIMIT ? OFFSET ?
    ");

    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados
    foreach ($especialidades as &$especialidade) {
        $especialidade['id'] = (int)$especialidade['id'];
        $especialidade['status'] = (int)$especialidade['status'];
        $especialidade['total_procedimentos'] = (int)$especialidade['total_procedimentos'];
    }

    // Calcular total de páginas
    $pages = ceil($total / $limit);

    ApiResponse::success([
        'items' => $especialidades,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);

} catch (PDOException $e) {
    error_log("Erro ao listar especialidades: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar especialidades');
}
