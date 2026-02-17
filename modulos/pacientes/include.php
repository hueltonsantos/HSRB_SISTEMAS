<?php
/**
 * Arquivo de inclusão do módulo de pacientes
 * Carrega os modelos e classes necessárias
 */

// Inclui o modelo de pacientes
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';

// Define as constantes do módulo
define('PACIENTES_MODULE_PATH', MODULES_PATH . '/pacientes');
define('PACIENTES_TEMPLATE_PATH', PACIENTES_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function pacientesProcessAction($action = '') {
    // Se a ação não for informada, usa a padrão
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    }
    
    // Define o arquivo a ser incluído
    switch ($action) {
        case 'new':
            $file = PACIENTES_MODULE_PATH . '/controllers/novo.php';
            break;
        case 'edit':
            $file = PACIENTES_MODULE_PATH . '/controllers/editar.php';
            break;
        case 'view':
            $file = PACIENTES_MODULE_PATH . '/controllers/visualizar.php';
            break;
        case 'delete':
            $file = PACIENTES_MODULE_PATH . '/controllers/deletar.php';
            break;
        case 'save':
            $file = PACIENTES_MODULE_PATH . '/controllers/salvar.php';
            break;
        case 'search':
            $file = PACIENTES_MODULE_PATH . '/controllers/buscar.php';
            break;
        case 'ajax_search':
            $file = PACIENTES_MODULE_PATH . '/controllers/ajax_search.php';
            break;
        case 'list':
        default:
            $file = PACIENTES_MODULE_PATH . '/controllers/listar.php';
            break;
    }
    
    // Inclui o arquivo e captura a saída
    ob_start();
    include $file;
    return ob_get_clean();
}