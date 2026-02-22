<?php
/**
 * Endpoint de Listagem de Clínicas
 * GET /api/clinics/list
 *
 * Query params: ?page=1&limit=20&search=nome&status=1
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "items": [...], "pagination": { "total": 150, "page": 1, "limit": 20, "pages": 8 } } }
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
        $where[] = '(nome LIKE ? OR cnpj LIKE ? OR cidade LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    // Filtro de status
    if ($status !== null) {
        $where[] = 'status = ?';
        $params[] = $status;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Contar total de registros
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM clinicas_parceiras $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    // Buscar clínicas
    $stmt = $pdo->prepare("
        SELECT
            id,
            nome,
            razao_social,
            cnpj,
            responsavel,
            endereco,
            numero,
            bairro,
            cidade,
            estado,
            cep,
            telefone,
            celular,
            email,
            site,
            tipo,
            percentual_repasse,
            status,
            data_cadastro
        FROM clinicas_parceiras
        $whereClause
        ORDER BY nome ASC
        LIMIT ? OFFSET ?
    ");

    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $clinicas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados
    foreach ($clinicas as &$clinica) {
        $clinica['id'] = (int)$clinica['id'];
        $clinica['status'] = (int)$clinica['status'];
        $clinica['percentual_repasse'] = $clinica['percentual_repasse'] !== null ? (float)$clinica['percentual_repasse'] : null;

        // Formatar CNPJ (XX.XXX.XXX/XXXX-XX)
        if ($clinica['cnpj']) {
            $cnpj = preg_replace('/\D/', '', $clinica['cnpj']);
            if (strlen($cnpj) === 14) {
                $clinica['cnpj_formatado'] = substr($cnpj, 0, 2) . '.' .
                                             substr($cnpj, 2, 3) . '.' .
                                             substr($cnpj, 5, 3) . '/' .
                                             substr($cnpj, 8, 4) . '-' .
                                             substr($cnpj, 12, 2);
            }
        }
    }

    // Calcular total de páginas
    $pages = ceil($total / $limit);

    ApiResponse::success([
        'items' => $clinicas,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);

} catch (PDOException $e) {
    error_log("Erro ao listar clínicas: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar clínicas');
}
