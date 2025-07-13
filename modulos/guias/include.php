<?php
/**
 * Arquivo de inclusão do módulo de guias
 * Carrega os modelos e classes necessárias
 */

// Define as constantes do módulo
define('GUIAS_MODULE_PATH', MODULES_PATH . '/guias');
define('GUIAS_TEMPLATE_PATH', GUIAS_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function guiasProcessAction($action = '') {
    // Se a ação não for informada, usa a padrão
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    }
    
    // Define o arquivo a ser incluído
    switch ($action) {
        case 'view':
            $file = GUIAS_MODULE_PATH . '/controllers/visualizar.php';
            break;
        case 'edit':
            $file = GUIAS_MODULE_PATH . '/controllers/editar.php';
            break;
        case 'cancel':
            $file = GUIAS_MODULE_PATH . '/controllers/cancelar.php';
            break;
        case 'print':
            $file = GUIAS_MODULE_PATH . '/controllers/imprimir.php';
            break;
        case 'list':
        default:
            $file = GUIAS_MODULE_PATH . '/controllers/listar.php';
            break;
    }
    
    // Inclui o arquivo e captura a saída
    ob_start();
    include $file;
    return ob_get_clean();
}