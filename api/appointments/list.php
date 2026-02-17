<?php
/**
 * Endpoint de Listagem de Agendamentos
 * GET /api/appointments/list
 *
 * Query params: ?page=1&limit=20&search=&status=&data_inicio=&data_fim=&clinica_id=&especialidade_id=
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
    $dataInicio = isset($_GET['data_inicio']) ? trim($_GET['data_inicio']) : '';
    $dataFim = isset($_GET['data_fim']) ? trim($_GET['data_fim']) : '';
    $clinicaId = isset($_GET['clinica_id']) ? (int)$_GET['clinica_id'] : null;
    $especialidadeId = isset($_GET['especialidade_id']) ? (int)$_GET['especialidade_id'] : null;

    // Construir query
    $where = [];
    $params = [];

    // Filtrar por clínica do usuário se necessário
    if ($user['clinica_id']) {
        $where[] = 'a.clinica_id = ?';
        $params[] = $user['clinica_id'];
    }

    // Filtro de busca por nome do paciente
    if ($search) {
        $where[] = '(p.nome LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
    }

    // Filtro de status
    if ($status) {
        $where[] = 'a.status_agendamento = ?';
        $params[] = $status;
    }

    // Filtro de data início
    if ($dataInicio) {
        $where[] = 'a.data_consulta >= ?';
        $params[] = $dataInicio;
    }

    // Filtro de data fim
    if ($dataFim) {
        $where[] = 'a.data_consulta <= ?';
        $params[] = $dataFim;
    }

    // Filtro de clínica (parâmetro explícito, só se o usuário não tem clínica fixa)
    if ($clinicaId && !$user['clinica_id']) {
        $where[] = 'a.clinica_id = ?';
        $params[] = $clinicaId;
    }

    // Filtro de especialidade
    if ($especialidadeId) {
        $where[] = 'a.especialidade_id = ?';
        $params[] = $especialidadeId;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Contar total de registros
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*)
        FROM agendamentos a
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        $whereClause
    ");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    // Buscar agendamentos com JOINs
    $stmt = $pdo->prepare("
        SELECT
            a.id,
            a.data_consulta,
            a.hora_consulta,
            a.status_agendamento AS status,
            a.observacoes,
            p.nome AS paciente_nome,
            p.cpf AS paciente_cpf,
            cp.nome AS clinica_nome,
            e.nome AS especialidade_nome,
            vp.procedimento AS procedimento_nome
        FROM agendamentos a
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        LEFT JOIN clinicas_parceiras cp ON a.clinica_id = cp.id
        LEFT JOIN especialidades e ON a.especialidade_id = e.id
        LEFT JOIN valores_procedimentos vp ON a.procedimento_id = vp.id
        $whereClause
        ORDER BY a.data_consulta DESC, a.hora_consulta DESC
        LIMIT ? OFFSET ?
    ");

    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados
    foreach ($agendamentos as &$agendamento) {
        $agendamento['id'] = (int)$agendamento['id'];
    }

    // Calcular total de páginas
    $pages = ceil($total / $limit);

    ApiResponse::success([
        'items' => $agendamentos,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);

} catch (PDOException $e) {
    error_log("Erro ao listar agendamentos: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar agendamentos');
}
