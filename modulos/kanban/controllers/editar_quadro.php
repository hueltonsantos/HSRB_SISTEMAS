<?php

/**
 * Controlador - Editar Quadro Kanban
 */

verificar_acesso('kanban_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Quadro nao informado'];
    header('Location: index.php?module=kanban');
    exit;
}

$model = new KanbanModel();
$quadro = $model->buscarQuadro((int) $_GET['id']);

if (!$quadro) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Quadro nao encontrado'];
    header('Location: index.php?module=kanban');
    exit;
}

include KANBAN_TEMPLATE_PATH . '/form_quadro.php';
