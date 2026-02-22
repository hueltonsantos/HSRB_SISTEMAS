<?php
require_once 'auth.php';
verificar_acesso('repasse_view');

$caixaModel = new CaixaModel();

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Filtros
$filtros = [];
if (!empty($_GET['clinica_id'])) $filtros['clinica_id'] = (int)$_GET['clinica_id'];
if (!empty($_GET['status'])) $filtros['status'] = $_GET['status'];
if (!empty($_GET['periodo_inicio'])) $filtros['periodo_inicio'] = $_GET['periodo_inicio'];
if (!empty($_GET['periodo_fim'])) $filtros['periodo_fim'] = $_GET['periodo_fim'];

$repasses = $caixaModel->listarRepasses($filtros, $limit, $offset);
$totalRepasses = $caixaModel->countRepasses($filtros);
$totalPages = ceil($totalRepasses / $limit);

$clinicas = $caixaModel->getClinicas();

include CAIXA_TEMPLATE_PATH . 'repasses.php';
