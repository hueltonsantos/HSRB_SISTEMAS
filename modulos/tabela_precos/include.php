<?php
require_once 'auth.php';
// Allows access to all for now (viewing public price list), 
// but creating/editing will be restricted in controllers if we add them.
// verificar_acesso('price_view'); 

define('PRECOS_PATH', __DIR__);
define('PRECOS_TEMPLATE_PATH', PRECOS_PATH . '/templates/');
define('PRECOS_CONTROLLER_PATH', PRECOS_PATH . '/controllers/');
define('PRECOS_MODEL_PATH', PRECOS_PATH . '/models/');

if (!class_exists('Model')) {
    require_once __DIR__ . '/../../Model.php';
}

require_once PRECOS_MODEL_PATH . 'PrecoModel.php';

function tabela_precosProcessAction($action) {
    ob_start();
    $controller_file = PRECOS_CONTROLLER_PATH . $action . '.php';
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        require_once PRECOS_CONTROLLER_PATH . 'listar.php';
    }
    return ob_get_clean();
}
