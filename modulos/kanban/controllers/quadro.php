<?php

/**
 * Controlador - Visualizar Quadro Kanban (Board View)
 */

verificar_acesso('kanban_view');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Quadro nao informado'];
    header('Location: index.php?module=kanban');
    exit;
}

$quadroId = (int) $_GET['id'];
$model = new KanbanModel();

$quadro = $model->buscarQuadro($quadroId);
if (!$quadro) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Quadro nao encontrado'];
    header('Location: index.php?module=kanban');
    exit;
}

$colunas = $model->listarColunas($quadroId);
$usuarios = $model->listarUsuarios();

// Carregar cards de cada coluna
foreach ($colunas as &$coluna) {
    $coluna['cards'] = $model->listarCards($coluna['id']);
}

include KANBAN_TEMPLATE_PATH . '/quadro.php';
