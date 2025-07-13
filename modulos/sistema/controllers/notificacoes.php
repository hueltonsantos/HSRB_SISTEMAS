<?php
/**
 * Controlador para listar e gerenciar notificações
 */

// Inclui o modelo de notificações
require_once MODULES_PATH . '/sistema/models/NotificacaoModel.php';
$notificacaoModel = new NotificacaoModel();

// Verificar se é uma solicitação AJAX para marcar todas como lidas
if (isset($_GET['acao']) && $_GET['acao'] == 'marcar_todas' && isset($_GET['ajax'])) {
    $resultado = $notificacaoModel->marcarTodasComoLidas();
    
    // Retorna JSON com o resultado
    header('Content-Type: application/json');
    echo json_encode(['success' => $resultado]);
    exit;
}

// Se uma ação foi solicitada (não-AJAX)
if (isset($_GET['acao'])) {
    // Marcar todas como lidas
    if ($_GET['acao'] == 'marcar_todas') {
        $notificacaoModel->marcarTodasComoLidas();
        
        // Redirecionar para a página anterior ou para a página inicial
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        header("Location: " . $redirect_url);
        exit;
    }
    
    // Marcar uma específica como lida
    if ($_GET['acao'] == 'marcar_lida' && isset($_GET['id'])) {
        $notificacaoModel->marcarComoLida($_GET['id']);
        
        // Redirecionar para a página anterior ou para a página de notificações
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?module=sistema&action=notificacoes';
        header("Location: " . $redirect_url);
        exit;
    }
}

// Determinar se deve mostrar todas as notificações ou apenas não lidas
$mostrarTodas = isset($_GET['mostrar']) && $_GET['mostrar'] == 'todas';

// Obter as notificações conforme o filtro
if ($mostrarTodas) {
    $notificacoes = $notificacaoModel->getAll([], 'data_criacao DESC');
    $titulo = 'Todas as Notificações';
} else {
    $notificacoes = $notificacaoModel->getAll(['lida' => 0], 'data_criacao DESC');
    $titulo = 'Notificações Não Lidas';
}

// O HTML do template começa aqui
?>