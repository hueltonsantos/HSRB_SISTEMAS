<?php
/**
 * Arquivo de inclusão do módulo sistema
 * Gerencia notificações e funcionalidades do sistema
 */

require_once MODULES_PATH . '/sistema/models/NotificacaoModel.php';

define('SISTEMA_MODULE_PATH', MODULES_PATH . '/sistema');
define('SISTEMA_TEMPLATE_PATH', SISTEMA_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function sistemaProcessAction($action = '') {
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'notificacoes';
    }

    switch ($action) {
        case 'notificacoes':
        default:
            $file = SISTEMA_MODULE_PATH . '/controllers/notificacoes.php';
            break;
    }

    ob_start();
    include $file;
    return ob_get_clean();
}
