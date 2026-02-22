<?php
define('RELATORIOS_PATH', __DIR__);
define('RELATORIOS_TEMPLATE_PATH', RELATORIOS_PATH . '/templates/');
define('RELATORIOS_CONTROLLER_PATH', RELATORIOS_PATH . '/controllers/');
define('RELATORIOS_MODEL_PATH', RELATORIOS_PATH . '/models/');

require_once RELATORIOS_MODEL_PATH . 'RelatorioModel.php';

function relatoriosProcessAction($action) {
    ob_start();
    $controller_file = RELATORIOS_CONTROLLER_PATH . $action . '.php';
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        require_once RELATORIOS_CONTROLLER_PATH . 'index.php';
    }
    return ob_get_clean();
}
