<?php
/**
 * Arquivo de inclusão do módulo de Logs
 */
require_once 'auth.php';
verificar_acesso('role_manage'); // Apenas admins podem ver logs

define('LOG_PATH', MODULES_PATH . '/log');
define('LOG_URL', 'index.php?module=log');
define('LOG_TEMPLATE_PATH', LOG_PATH . '/templates/');
define('LOG_MODEL_PATH', LOG_PATH . '/models/');

// Carrega o Model
require_once LOG_MODEL_PATH . 'LogModel.php';

function logProcessAction($action) {
    ob_start();

    switch ($action) {
        case 'detalhes':
            require_once LOG_PATH . '/controllers/detalhes.php';
            break;
        case 'listar':
        case 'list':
        default:
            require_once LOG_PATH . '/controllers/listar.php';
            break;
    }

    return ob_get_clean();
}
