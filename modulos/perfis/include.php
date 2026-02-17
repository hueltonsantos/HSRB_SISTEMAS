<?php
require_once 'auth.php';
verificar_acesso('role_manage'); // Requires 'role_manage' permission

define('PERFIS_PATH', __DIR__);
define('PERFIS_TEMPLATE_PATH', PERFIS_PATH . '/templates/');
define('PERFIS_CONTROLLER_PATH', PERFIS_PATH . '/controllers/');
define('PERFIS_MODEL_PATH', PERFIS_PATH . '/models/');

if (!class_exists('Model')) {
    require_once __DIR__ . '/../../Model.php';
}

require_once PERFIS_MODEL_PATH . 'PerfilModel.php';

function perfisProcessAction($action) {
    ob_start();
    $controller_file = PERFIS_CONTROLLER_PATH . $action . '.php';
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        require_once PERFIS_CONTROLLER_PATH . 'list.php';
    }
    return ob_get_clean();
}
