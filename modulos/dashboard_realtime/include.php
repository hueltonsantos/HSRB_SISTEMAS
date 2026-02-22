<?php
/**
 * Módulo Dashboard em Tempo Real
 * Painel com gráficos e relatórios em tempo real
 */

require_once 'auth.php';
verificar_acesso('dashboard_realtime');

// Define constantes do módulo
define('DASHBOARD_REALTIME_PATH', __DIR__);
define('DASHBOARD_REALTIME_TEMPLATE_PATH', DASHBOARD_REALTIME_PATH . '/templates/');
define('DASHBOARD_REALTIME_CONTROLLER_PATH', DASHBOARD_REALTIME_PATH . '/controllers/');
define('DASHBOARD_REALTIME_MODEL_PATH', DASHBOARD_REALTIME_PATH . '/models/');

// Carrega a classe Model base se não estiver carregada
if (!class_exists('Model')) {
    require_once ROOT_PATH . '/Model.php';
}

// Carrega o modelo
require_once DASHBOARD_REALTIME_MODEL_PATH . 'DashboardRealtimeModel.php';

/**
 * Processa a ação solicitada
 * @param string $action Ação a ser executada
 * @return string HTML gerado
 */
function dashboard_realtimeProcessAction($action) {
    ob_start();

    $controller_file = DASHBOARD_REALTIME_CONTROLLER_PATH . $action . '.php';

    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        require_once DASHBOARD_REALTIME_CONTROLLER_PATH . 'index.php';
    }

    return ob_get_clean();
}
