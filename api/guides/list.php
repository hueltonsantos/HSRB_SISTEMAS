<?php
/**
 * Endpoint de Listagem de Guias de Encaminhamento
 * GET /api/guides/list
 *
 * Query params: ?page=1&limit=20&search=nome_ou_codigo&status=agendado
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
requirePermission($user, 'appointment_view');

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
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';

    // Construir query
    $where = [];
    $params = [];

    // Filtro de busca (por nome do paciente ou código da guia)
    if ($search) {
        $where[] = '(p.nome LIKE ? OR g.codigo LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    // Filtro de status
    if ($status) {
        $where[] = 'g.status = ?';
        $params[] = $status;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Contar total de registros
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*)
        FROM guias_encaminhamento g
        LEFT JOIN pacientes p ON g.paciente_id = p.id
        $whereClause
    ");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    // Buscar guias com JOINs
    $stmt = $pdo->prepare("
        SELECT
            g.id,
            g.codigo,
            g.data_agendamento,
            g.horario_agendamento,
            g.status,
            g.data_emissao,
            p.nome AS paciente_nome,
            p.cpf AS paciente_cpf,
            vp.procedimento AS procedimento_nome,
            vp.valor_paciente AS valor,
            e.nome AS especialidade_nome
        FROM guias_encaminhamento g
        LEFT JOIN pacientes p ON g.paciente_id = p.id
        LEFT JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
        LEFT JOIN especialidades e ON vp.especialidade_id = e.id
        $whereClause
        ORDER BY g.data_emissao DESC
        LIMIT ? OFFSET ?
    ");

    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $guias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados
    foreach ($guias as &$guia) {
        $guia['id'] = (int)$guia['id'];
        $guia['valor'] = $guia['valor'] !== null ? (float)$guia['valor'] : null;

        // Formatar CPF (XXX.XXX.XXX-XX)
        if ($guia['paciente_cpf']) {
            $cpf = preg_replace('/\D/', '', $guia['paciente_cpf']);
            if (strlen($cpf) === 11) {
                $guia['paciente_cpf_formatado'] = substr($cpf, 0, 3) . '.' .
                                                   substr($cpf, 3, 3) . '.' .
                                                   substr($cpf, 6, 3) . '-' .
                                                   substr($cpf, 9, 2);
            }
        }
    }

    // Calcular total de páginas
    $pages = ceil($total / $limit);

    ApiResponse::success([
        'items' => $guias,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);

} catch (PDOException $e) {
    error_log("Erro ao listar guias: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar guias');
}
