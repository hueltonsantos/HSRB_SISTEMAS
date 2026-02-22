<?php
require_once 'auth.php';
verificar_acesso('caixa_view');

$caixaModel = new CaixaModel();

// Configurações de paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Filtros
$filtros = [];
if (!empty($_GET['data_inicio'])) $filtros['data_inicio'] = $_GET['data_inicio'];
if (!empty($_GET['data_fim'])) $filtros['data_fim'] = $_GET['data_fim'];
if (!empty($_GET['tipo'])) $filtros['tipo'] = $_GET['tipo'];
if (!empty($_GET['forma_pagamento'])) $filtros['forma_pagamento'] = $_GET['forma_pagamento'];

// Busca dados
$lancamentos = $caixaModel->listarLancamentos($filtros, $limit, $offset);
$totalLancamentos = $caixaModel->countLancamentos($filtros);
$totalPages = ceil($totalLancamentos / $limit);

// Resumo do dia
$resumoDia = $caixaModel->getResumoDia(date('d/m/Y'));

// Caixa aberto
$caixaAberto = $caixaModel->getCaixaAberto();

// Formas de pagamento para filtro
$formasPagamento = $caixaModel->getFormasPagamento();

// Agendamentos do dia (para lançar no caixa)
$agendamentosDoDia = $caixaModel->getAgendamentosDoDia();

include CAIXA_TEMPLATE_PATH . 'listar.php';
