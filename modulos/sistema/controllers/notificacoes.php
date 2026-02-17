<?php
/**
 * Controlador para listar e gerenciar notificações
 */

require_once MODULES_PATH . '/sistema/models/NotificacaoModel.php';
$notificacaoModel = new NotificacaoModel();

// Requisição AJAX para marcar todas como lidas
if (isset($_GET['acao']) && $_GET['acao'] == 'marcar_todas' && isset($_GET['ajax'])) {
    $notificacaoModel->marcarTodasComoLidas();
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Requisição AJAX para contar notificações não lidas (polling)
if (isset($_GET['acao']) && $_GET['acao'] == 'contar' && isset($_GET['ajax'])) {
    $total = $notificacaoModel->contarNotificacoesNaoLidas();
    header('Content-Type: application/json');
    echo json_encode(['total' => $total]);
    exit;
}

// Ações não-AJAX
if (isset($_GET['acao'])) {
    if ($_GET['acao'] == 'marcar_todas') {
        $notificacaoModel->marcarTodasComoLidas();
        header("Location: index.php?module=sistema&action=notificacoes");
        exit;
    }

    if ($_GET['acao'] == 'marcar_lida' && isset($_GET['id'])) {
        $notificacaoModel->marcarComoLida((int)$_GET['id']);
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?module=sistema&action=notificacoes';
        header("Location: " . $redirect);
        exit;
    }
}

// Paginação e filtros para a listagem
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todas';
$porPagina = 15;

$resultado = $notificacaoModel->getNotificacoesPaginadas($pagina, $porPagina, $filtro);
$notificacoes = $resultado['notificacoes'];
$total = $resultado['total'];
$totalPaginas = $resultado['totalPaginas'];
$totalNaoLidas = $notificacaoModel->contarNotificacoesNaoLidas();

include SISTEMA_TEMPLATE_PATH . '/notificacoes.php';
