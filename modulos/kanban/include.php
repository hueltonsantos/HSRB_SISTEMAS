<?php

/**
 * Arquivo de inclusao do modulo Kanban
 */

define('KANBAN_MODULE_PATH', MODULES_PATH . '/kanban');
define('KANBAN_MODEL_PATH', KANBAN_MODULE_PATH . '/models/');
define('KANBAN_TEMPLATE_PATH', KANBAN_MODULE_PATH . '/templates');

require_once KANBAN_MODEL_PATH . 'KanbanModel.php';

function kanbanProcessAction($action = '')
{
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    }

    switch ($action) {
        case 'quadro':
            $file = KANBAN_MODULE_PATH . '/controllers/quadro.php';
            break;
        case 'novo_quadro':
            $file = KANBAN_MODULE_PATH . '/controllers/novo_quadro.php';
            break;
        case 'editar_quadro':
            $file = KANBAN_MODULE_PATH . '/controllers/editar_quadro.php';
            break;
        case 'salvar_quadro':
            $file = KANBAN_MODULE_PATH . '/controllers/salvar_quadro.php';
            break;
        case 'excluir_quadro':
            $file = KANBAN_MODULE_PATH . '/controllers/excluir_quadro.php';
            break;
        case 'api':
            $file = KANBAN_MODULE_PATH . '/controllers/api.php';
            break;
        default:
            $file = KANBAN_MODULE_PATH . '/controllers/listar.php';
            break;
    }

    ob_start();
    include $file;
    return ob_get_clean();
}
