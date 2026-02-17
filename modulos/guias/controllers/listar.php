<?php
require_once 'auth.php';
verificar_acesso('appointment_view');

/**
 * Controlador para listar guias de encaminhamento
 */

// Parâmetros de paginação
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Filtros
$filtros = [];
if (isset($_GET['paciente']) && !empty($_GET['paciente'])) {
    $filtros['paciente'] = $_GET['paciente'];
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $filtros['status'] = $_GET['status'];
}
if (isset($_GET['data_inicio']) && !empty($_GET['data_inicio'])) {
    $filtros['data_inicio'] = $_GET['data_inicio'];
}
if (isset($_GET['data_fim']) && !empty($_GET['data_fim'])) {
    $filtros['data_fim'] = $_GET['data_fim'];
}

try {
    require_once 'config.php';
    require_once 'Database.php';
    
    $db = Database::getInstance()->getConnection();



    // Modifique a consulta SQL para:
    $sql = "
    SELECT g.*, p.nome as paciente_nome, p.cpf as paciente_documento,
           vp.procedimento as procedimento_nome, e.nome as especialidade_nome,
           cp.nome as clinica_nome
    FROM guias_encaminhamento g
    INNER JOIN pacientes p ON g.paciente_id = p.id
    INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
    INNER JOIN especialidades e ON vp.especialidade_id = e.id
    LEFT JOIN clinicas_parceiras cp ON e.id = cp.id
    WHERE 1=1
";

// // Constrói a consulta SQL
    // $sql = "
    //     SELECT g.*, p.nome as paciente_nome, p.documento as paciente_documento,
    //            vp.procedimento as procedimento_nome, e.nome as especialidade_nome,
    //            cp.nome as clinica_nome
    //     FROM guias_encaminhamento g
    //     INNER JOIN pacientes p ON g.paciente_id = p.id
    //     INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
    //     INNER JOIN especialidades e ON vp.especialidade_id = e.id
    //     LEFT JOIN clinicas_parceiras cp ON e.id = cp.especialidade_id
    //     WHERE 1=1
    // ";

    $params = [];

    // Adiciona os filtros à consulta
    if (isset($filtros['paciente'])) {
        $sql .= " AND (p.nome LIKE ? OR p.documento LIKE ?)";
        $params[] = "%{$filtros['paciente']}%";
        $params[] = "%{$filtros['paciente']}%";
    }

    if (isset($filtros['status'])) {
        $sql .= " AND g.status = ?";
        $params[] = $filtros['status'];
    }

    if (isset($filtros['data_inicio'])) {
        $sql .= " AND g.data_agendamento >= ?";
        $params[] = $filtros['data_inicio'];
    }

    if (isset($filtros['data_fim'])) {
        $sql .= " AND g.data_agendamento <= ?";
        $params[] = $filtros['data_fim'];
    }

    // Contagem total para paginação
    $stmtCount = $db->prepare("SELECT COUNT(*) FROM ({$sql}) as total");
    $stmtCount->execute($params);
    $totalGuias = $stmtCount->fetchColumn();

    // Adiciona ordenação e paginação
    $sql .= " ORDER BY g.data_agendamento DESC, g.id DESC LIMIT {$limit} OFFSET {$offset}";

    // Executa a consulta
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $guias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cálculos para paginação
    $totalPages = ceil($totalGuias / $limit);
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao listar guias: ' . $e->getMessage()
    ];

    // Em caso de erro, inicializa as variáveis vazias
    $guias = [];
    $totalGuias = 0;
    $totalPages = 1;
}

// Define o título da página
$pageTitle = "Guias de Encaminhamento";

// Inclui o template
include GUIAS_TEMPLATE_PATH . '/listar.php';
