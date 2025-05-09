<?php
/**
 * Arquivo de inclusão do módulo de especialidades
 * Carrega os modelos e classes necessárias
 */

// Inclui os modelos
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';
require_once MODULES_PATH . '/especialidades/models/ValorProcedimentoModel.php';
require_once MODULES_PATH . '/especialidades/models/ProcedimentoModel.php'; 

// Define as constantes do módulo
define('ESPECIALIDADES_MODULE_PATH', MODULES_PATH . '/especialidades');
define('ESPECIALIDADES_TEMPLATE_PATH', ESPECIALIDADES_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function especialidadesProcessAction($action = '') {
    // Se a ação não for informada, usa a padrão
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    }
    
    // Define o arquivo a ser incluído
    switch ($action) {
        case 'new':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/novo.php';
            break;
        case 'edit':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/editar.php';
            break;
        case 'view':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/visualizar.php';
            break;
        case 'delete':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/deletar.php';
            break;
        case 'save':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/salvar.php';
            break;
        case 'procedimentos':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/procedimentos.php';
            break;
        case 'add_procedimento':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/adicionar_procedimento.php';
            break;
        case 'edit_procedimento':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/editar_procedimento.php';
            break;
        case 'save_procedimento':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/salvar_procedimento.php';
            break;
        case 'delete_procedimento':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/deletar_procedimento.php';
            break;
        case 'batch_procedimentos':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/lote_procedimentos.php';
            break;
        case 'save_batch_procedimentos':
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/salvar_lote_procedimentos.php';
            break;
        case 'list':
        default:
            $file = ESPECIALIDADES_MODULE_PATH . '/controllers/listar.php';
            break;
    }
    
    // Inclui o arquivo e captura a saída
    ob_start();
    include $file;
    return ob_get_clean();
}