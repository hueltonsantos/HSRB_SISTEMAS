<?php
/**
 * Arquivo de inclusão do módulo de agendamentos
 * Carrega os modelos e classes necessárias
 */

// Inclui o modelo de agendamentos
require_once MODULES_PATH . '/agendamentos/models/AgendamentoModel.php';

// Define as constantes do módulo
define('AGENDAMENTOS_MODULE_PATH', MODULES_PATH . '/agendamentos');
define('AGENDAMENTOS_TEMPLATE_PATH', AGENDAMENTOS_MODULE_PATH . '/templates');

/**
 * Função para processar a ação solicitada
 * @param string $action
 * @return string
 */
function agendamentosProcessAction($action = '') {
    // Se a ação não for informada, usa a padrão
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    }
    
    // Define o arquivo a ser incluído
    switch ($action) {
        case 'new':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/novo.php';
            break;
        case 'edit':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/editar.php';
            break;
        case 'view':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/visualizar.php';
            break;
        case 'delete':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/deletar.php';
            break;
        case 'save':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/salvar.php';
            break;
        case 'calendar':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/calendario.php';
            break;
        case 'get_especialidades':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/get_especialidades.php';
            break;
        case 'get_horarios':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/get_horarios.php';
            break;
        case 'update_status':
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/atualizar_status.php';
            break;
        case 'list':
        default:
            $file = AGENDAMENTOS_MODULE_PATH . '/controllers/listar.php';
            break;
    }
    
    // Inclui o arquivo e captura a saída
    ob_start();
    include $file;
    return ob_get_clean();
}