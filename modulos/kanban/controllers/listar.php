<?php

/**
 * Controlador - Listar Quadros Kanban
 */

verificar_acesso('kanban_view');

$model = new KanbanModel();
$quadros = $model->listarQuadros($_SESSION['usuario_id']);

include KANBAN_TEMPLATE_PATH . '/listar.php';
