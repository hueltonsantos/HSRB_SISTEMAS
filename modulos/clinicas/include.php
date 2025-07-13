<?php
/**
 * Arquivo de inclusão do módulo de clínicas
 * Carrega os modelos e classes necessárias
 */

// Inclui o modelo de clínicas
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';

// Define as constantes do módulo
define('CLINICAS_MODULE_PATH', MODULES_PATH . '/clinicas');
define('CLINICAS_TEMPLATE_PATH', CLINICAS_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function clinicasProcessAction($action = '') {
    // Se a ação não for informada, usa a padrão
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    }
    
    // Define o arquivo a ser incluído
    switch ($action) {
        case 'new':
            $file = CLINICAS_MODULE_PATH . '/controllers/novo.php';
            break;
        case 'edit':
            $file = CLINICAS_MODULE_PATH . '/controllers/editar.php';
            break;
        case 'view':
            $file = CLINICAS_MODULE_PATH . '/controllers/visualizar.php';
            break;
        case 'delete':
            $file = CLINICAS_MODULE_PATH . '/controllers/deletar.php';
            break;
        case 'save':
            $file = CLINICAS_MODULE_PATH . '/controllers/salvar.php';
            break;
        case 'search':
            $file = CLINICAS_MODULE_PATH . '/controllers/buscar.php';
            break;
        case 'especialidades':
            $file = CLINICAS_MODULE_PATH . '/controllers/especialidades.php';
            break;
        case 'save_especialidades':
            $file = CLINICAS_MODULE_PATH . '/controllers/salvar_especialidades.php';
            break;
        case 'list':
        default:
            $file = CLINICAS_MODULE_PATH . '/controllers/listar.php';
            break;
    }
    
    // Inclui o arquivo e captura a saída
    ob_start();
    include $file;
    return ob_get_clean();
}