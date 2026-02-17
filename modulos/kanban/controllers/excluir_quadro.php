<?php

/**
 * Controlador - Excluir Quadro Kanban
 */

verificar_acesso('kanban_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Quadro nao informado'];
    header('Location: index.php?module=kanban');
    exit;
}

$model = new KanbanModel();
$quadro = $model->buscarQuadro((int) $_GET['id']);

if ($quadro) {
    $model->excluirQuadro($quadro['id']);
    registrarLog('excluir', 'kanban', "Quadro '{$quadro['nome']}' excluido", $quadro['id']);
    $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Quadro excluido com sucesso!'];
} else {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Quadro nao encontrado'];
}

header('Location: index.php?module=kanban');
exit;
