<?php
require_once 'auth.php';

define('CAIXA_PATH', __DIR__);
define('CAIXA_TEMPLATE_PATH', CAIXA_PATH . '/templates/');
define('CAIXA_CONTROLLER_PATH', CAIXA_PATH . '/controllers/');
define('CAIXA_MODEL_PATH', CAIXA_PATH . '/models/');

if (!class_exists('Model')) {
    require_once __DIR__ . '/../../Model.php';
}

require_once CAIXA_MODEL_PATH . 'CaixaModel.php';

function caixaProcessAction($action)
{
    ob_start();
    $controller_file = CAIXA_CONTROLLER_PATH . $action . '.php';
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        require_once CAIXA_CONTROLLER_PATH . 'listar.php';
    }
    return ob_get_clean();
}
