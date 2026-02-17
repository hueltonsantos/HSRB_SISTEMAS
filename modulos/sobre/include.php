<?php

/**
 * Arquivo de inclusão do módulo Sobre o Sistema
 */

define('SOBRE_MODULE_PATH', MODULES_PATH . '/sobre');
define('SOBRE_TEMPLATE_PATH', SOBRE_MODULE_PATH . '/templates');

function sobreProcessAction($action = '')
{
    ob_start();
    include SOBRE_MODULE_PATH . '/controllers/index.php';
    return ob_get_clean();
}
