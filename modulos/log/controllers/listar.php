<?php
$logModel = new LogModel();

// Filtros
$filtros = [
    'usuario_id' => isset($_GET['usuario_id']) ? $_GET['usuario_id'] : '',
    'acao' => isset($_GET['acao']) ? $_GET['acao'] : '',
    'modulo' => isset($_GET['modulo']) ? $_GET['modulo'] : '',
    'data_inicio' => isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '',
    'data_fim' => isset($_GET['data_fim']) ? $_GET['data_fim'] : '',
    'busca' => isset($_GET['busca']) ? $_GET['busca'] : ''
];

// Paginação
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$porPagina = 50;
$offset = ($pagina - 1) * $porPagina;

// Buscar logs
$logs = $logModel->listar($filtros, $porPagina, $offset);
$total = $logModel->contarTotal($filtros);
$totalPaginas = ceil($total / $porPagina);

// Dados para filtros
$acoes = $logModel->getAcoesDistintas();
$modulos = $logModel->getModulosDistintos();

// Lista de usuários para filtro
$db = Database::getInstance();
$usuarios = $db->fetchAll("SELECT id, nome FROM usuarios ORDER BY nome");

require_once LOG_TEMPLATE_PATH . 'listar.php';
