<?php
/**
 * Modulo: Minha Clinica (Master)
 * Gerenciamento interno da clinica principal
 */

// Carregar model
// Carregar models
require_once MODULES_PATH . '/minha_clinica/models/MinhaClinicaModel.php';
require_once MODULES_PATH . '/minha_clinica/models/ConveniosModel.php';
require_once MODULES_PATH . '/minha_clinica/models/FinanceiroModel.php';
require_once MODULES_PATH . '/minha_clinica/models/ProfissionaisConfigModel.php';
require_once MODULES_PATH . '/minha_clinica/models/ProntuarioModel.php';

// Definir constantes do modulo
define('MINHA_CLINICA_PATH', MODULES_PATH . '/minha_clinica');
define('MINHA_CLINICA_CONTROLLERS_PATH', MINHA_CLINICA_PATH . '/controllers');
define('MINHA_CLINICA_TEMPLATES_PATH', MINHA_CLINICA_PATH . '/templates');

/**
 * Funcao para processar a acao solicitada
 * @param string $action
 * @return string
 */
function minha_clinicaProcessAction($action = '')
{
    // Se a acao nao for informada, usa a padrao
    if (empty($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    }

    // Define o arquivo a ser incluido
    switch ($action) {
        // Dashboard
        case 'index':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/index.php';
            break;

        // Agendamentos
        case 'agendamentos':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/agendamentos/listar.php';
            break;
        case 'novo_agendamento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/agendamentos/novo.php';
            break;
        case 'editar_agendamento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/agendamentos/editar.php';
            break;
        case 'salvar_agendamento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/agendamentos/salvar.php';
            break;
        case 'ver_agendamento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/agendamentos/ver.php';
            break;

        // Especialidades
        case 'especialidades':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/especialidades/listar.php';
            break;
        case 'nova_especialidade':
        case 'editar_especialidade':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/especialidades/form.php';
            break;
        case 'salvar_especialidade':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/especialidades/salvar.php';
            break;
        case 'deletar_especialidade':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/especialidades/deletar.php';
            break;

        // Procedimentos
        case 'procedimentos':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/procedimentos/listar.php';
            break;
        case 'novo_procedimento':
        case 'editar_procedimento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/procedimentos/form.php';
            break;
        case 'salvar_procedimento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/procedimentos/salvar.php';
            break;
        case 'deletar_procedimento':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/procedimentos/deletar.php';
            break;

        // Profissionais
        case 'profissionais':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/profissionais/listar.php';
            break;
        case 'novo_profissional':
        case 'editar_profissional':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/profissionais/form.php';
            break;
        case 'salvar_profissional':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/profissionais/salvar.php';
            break;
        case 'deletar_profissional':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/profissionais/deletar.php';
            break;

        // API AJAX
        case 'api':
        case 'get_procedimentos':
        case 'get_horarios':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/api.php';
            break;

        // Convênios
        case 'convenios':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/convenios/listar.php';
            break;
        case 'novo_convenio':
        case 'editar_convenio':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/convenios/form.php';
            break;
        case 'salvar_convenio':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/convenios/salvar.php';
            break;
        case 'deletar_convenio':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/convenios/deletar.php';
            break;

        // Tabela de Preços
        case 'tabela_precos':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/tabela_precos/listar.php';
            break;
        case 'salvar_preco_ajax':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/tabela_precos/salvar.php';
            break;

        // Guias
        case 'guias':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/guias/listar.php';
            break;
        case 'alterar_status_guia':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/guias/alterar_status.php';
            break;

        // Painel do Profissional
        case 'painel_profissional':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/painel/dashboard.php';
            break;
        case 'config_profissionais':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/profissionais/config.php';
            break;
        case 'prontuario_paciente':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/prontuario/form.php';
            break;
        case 'salvar_evolucao':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/prontuario/salvar.php';
            break;
        case 'imprimir_evolucao':
            require_once MINHA_CLINICA_CONTROLLERS_PATH . '/prontuario/imprimir_evolucao.php';
            exit; // Interrompe para não carregar o layout do dashboard
            break;
        case 'prontuario_visualizar':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/prontuario/visualizar.php';
            break;
        case 'imprimir_prontuario':
            require_once MINHA_CLINICA_CONTROLLERS_PATH . '/prontuario/imprimir_prontuario.php';
            exit;
            break;

        // Financeiro
        case 'repasses':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/financeiro/repasses.php';
            break;
        case 'fechar_repasse':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/financeiro/fechar_repasse.php';
            break;
        case 'financeiro_dashboard':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/financeiro/dashboard.php';
            break;
        case 'financeiro_inadimplencia':
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/financeiro/inadimplencia.php';
            break;

        default:
            $file = MINHA_CLINICA_CONTROLLERS_PATH . '/index.php';
            break;
    }

    // Inclui o arquivo e captura a saida
    ob_start();
    if (file_exists($file)) {
        include $file;
    } else {
        echo '<div class="alert alert-danger">Controller nao encontrado: ' . htmlspecialchars($action) . '</div>';
    }
    return ob_get_clean();
}
