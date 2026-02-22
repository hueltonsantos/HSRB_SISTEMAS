<?php
/**
 * Endpoint de Listagem de Preços de Procedimentos
 * GET /api/prices/list
 * Query params: ?page=1&limit=20&search=nome&especialidade_id=1&status=1
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'price_manage');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $especialidadeId = isset($_GET['especialidade_id']) ? (int)$_GET['especialidade_id'] : null;
    $status = isset($_GET['status']) ? (int)$_GET['status'] : null;
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = 'vp.procedimento LIKE ?';
        $params[] = "%$search%";
    }
    
    if ($especialidadeId) {
        $where[] = 'vp.especialidade_id = ?';
        $params[] = $especialidadeId;
    }
    
    if ($status !== null) {
        $where[] = 'vp.status = ?';
        $params[] = $status;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM valores_procedimentos vp $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();
    
    $stmt = $pdo->prepare("
        SELECT 
            vp.id,
            vp.especialidade_id,
            vp.procedimento,
            vp.valor_paciente,
            vp.valor_repasse,
            vp.status,
            e.nome AS especialidade_nome
        FROM valores_procedimentos vp
        LEFT JOIN especialidades e ON vp.especialidade_id = e.id
        $whereClause
        ORDER BY e.nome ASC, vp.procedimento ASC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $precos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($precos as &$preco) {
        $preco['id'] = (int)$preco['id'];
        $preco['especialidade_id'] = (int)$preco['especialidade_id'];
        $preco['valor_paciente'] = (float)$preco['valor_paciente'];
        $preco['valor_repasse'] = (float)$preco['valor_repasse'];
        $preco['status'] = (int)$preco['status'];
        
        // Calcular percentual de repasse
        if ($preco['valor_paciente'] > 0) {
            $preco['percentual_repasse'] = round(($preco['valor_repasse'] / $preco['valor_paciente']) * 100, 2);
        } else {
            $preco['percentual_repasse'] = 0;
        }
    }
    
    $pages = ceil($total / $limit);
    
    ApiResponse::success([
        'items' => $precos,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao listar preços: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar preços');
}
