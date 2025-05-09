<?php
/**
 * Controlador para listagem de clínicas
 */

// Instancia o modelo de clínicas
$clinicaModel = new ClinicaModel();

// Configurações de paginação
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Filtros de busca
$filters = [];
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $filters['nome'] = $_GET['nome'];
}
if (isset($_GET['cnpj']) && !empty($_GET['cnpj'])) {
    $filters['cnpj'] = $_GET['cnpj'];
}
if (isset($_GET['cidade']) && !empty($_GET['cidade'])) {
    $filters['cidade'] = $_GET['cidade'];
}
if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $filters['estado'] = $_GET['estado'];
}

// Por padrão, mostra apenas clínicas ativas
if (!isset($_GET['status'])) {
    $filters['status'] = 1;
} else {
    $filters['status'] = (int) $_GET['status'];
}

// Busca as clínicas com os filtros
$clinicas = $clinicaModel->searchClinicas($filters, $limit, $offset);

// Conta o total de clínicas para a paginação
$totalClinicas = $clinicaModel->count($filters);
$totalPages = ceil($totalClinicas / $limit);

// Inclui o template de listagem
include CLINICAS_TEMPLATE_PATH . '/listar.php';