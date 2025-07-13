<?php
/**
 * Controlador para listagem de especialidades
 */

// Instancia o modelo de especialidades
$especialidadeModel = new EspecialidadeModel();

// Configurações de paginação
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Filtros de busca
$filters = [];
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $filters['nome'] = $_GET['nome'];
}

// Por padrão, mostra apenas especialidades ativas
if (!isset($_GET['status'])) {
    $filters['status'] = 1;
} else {
    $filters['status'] = (int) $_GET['status'];
}

// Busca as especialidades com os filtros
$especialidades = $especialidadeModel->searchEspecialidades($filters, $limit, $offset);

// Conta o total de especialidades para a paginação
$totalEspecialidades = $especialidadeModel->count($filters);
$totalPages = ceil($totalEspecialidades / $limit);

// Inclui o template de listagem
include ESPECIALIDADES_TEMPLATE_PATH . '/listar.php';