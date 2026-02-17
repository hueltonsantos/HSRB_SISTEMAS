<?php
require_once 'auth.php';
verificar_acesso('appointment_view');

// Instancia o modelo de pacientes
$pacienteModel = new PacienteModel();

// Configurações de paginação
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Filtros de busca
$filters = [];
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $filters['nome'] = $_GET['nome'];
}
if (isset($_GET['cpf']) && !empty($_GET['cpf'])) {
    $filters['cpf'] = $_GET['cpf'];
}
if (isset($_GET['cidade']) && !empty($_GET['cidade'])) {
    $filters['cidade'] = $_GET['cidade'];
}
if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $filters['estado'] = $_GET['estado'];
}

// Por padrão, mostra apenas pacientes ativos
if (!isset($_GET['status'])) {
    $filters['status'] = 1;
} else {
    $filters['status'] = (int) $_GET['status'];
}

// Busca os pacientes com os filtros
$pacientes = $pacienteModel->searchPacientes($filters, $limit, $offset);

// Conta o total de pacientes para a paginação
$totalPacientes = $pacienteModel->count($filters);
$totalPages = ceil($totalPacientes / $limit);

// Inclui o template de listagem
include PACIENTES_TEMPLATE_PATH . '/listar.php';