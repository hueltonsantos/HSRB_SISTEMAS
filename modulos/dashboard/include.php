<?php
/**
 * Arquivo de inclusão do módulo de dashboard
 */

// Define as constantes do módulo
define('DASHBOARD_MODULE_PATH', MODULES_PATH . '/dashboard');
define('DASHBOARD_TEMPLATE_PATH', DASHBOARD_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function dashboardProcessAction($action = '') {
    // Define o arquivo a ser incluído
    $file = DASHBOARD_MODULE_PATH . '/controllers/index.php';
    
    // Inclui o arquivo e captura a saída
    ob_start();
    include $file;
    return ob_get_clean();
}