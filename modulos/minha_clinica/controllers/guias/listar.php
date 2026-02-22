<?php
/**
 * Listagem de Guias - Minha Clinica
 */

if (!hasPermission('minha_clinica_ver')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();

// Filtros
$status = $_GET['status'] ?? '';
$convenioId = $_GET['convenio_id'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim = $_GET['data_fim'] ?? date('Y-m-t');

// Query base
$sql = "SELECT g.*, 
               p.nome as paciente_nome, 
               c.nome_fantasia as convenio_nome,
               prof.nome as profissional_nome,
               a.data_consulta, a.hora_consulta,
               (SELECT GROUP_CONCAT(proc.procedimento SEPARATOR ', ') 
                FROM master_agendamento_procedimentos map 
                JOIN master_procedimentos proc ON map.procedimento_id = proc.id 
                WHERE map.agendamento_id = g.agendamento_id) as procedimentos_lista
        FROM master_guias g
        JOIN pacientes p ON g.paciente_id = p.id
        JOIN master_convenios c ON g.convenio_id = c.id
        JOIN master_profissionais prof ON g.profissional_id = prof.id
        LEFT JOIN master_agendamentos a ON g.agendamento_id = a.id
        WHERE 1=1";

$params = [];

if ($status) {
    $sql .= " AND g.status = ?";
    $params[] = $status;
}

if ($convenioId) {
    $sql .= " AND g.convenio_id = ?";
    $params[] = $convenioId;
}

if ($dataInicio && $dataFim) {
    $sql .= " AND a.data_consulta BETWEEN ? AND ?";
    $params[] = $dataInicio;
    $params[] = $dataFim;
}

$sql .= " ORDER BY a.data_consulta DESC, a.hora_consulta DESC";

try {
    $guias = $db->fetchAll($sql, $params);
} catch (Exception $e) {
    echo "Erro SQL ao buscar guias: " . $e->getMessage();
    exit;
}

// Carregar convenios para filtro
$sqlConvenios = "SELECT id, nome_fantasia FROM master_convenios ORDER BY nome_fantasia";
$convenios = $db->fetchAll($sqlConvenios);

$pageTitle = 'Gestão de Guias e Autorizações';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/guias/listar.php';
