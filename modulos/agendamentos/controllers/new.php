<?php
/**
 * Controlador para o formulário de novo agendamento
 */

// Inclui modelos relacionados
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';
require_once MODULES_PATH . '/especialidades/models/ValorProcedimentoModel.php';

$pacienteModel = new PacienteModel();
$clinicaModel = new ClinicaModel();
$especialidadeModel = new EspecialidadeModel();
$procedimentoModel = new ValorProcedimentoModel();

// Verifica se há dados de formulário na sessão (em caso de erro)
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];

// Limpa os dados da sessão
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Novo Agendamento";

// Pré-seleção de paciente, clínica ou especialidade (se fornecidos na URL)
if (isset($_GET['paciente_id']) && !empty($_GET['paciente_id'])) {
    $formData['paciente_id'] = (int) $_GET['paciente_id'];
    
    // Busca o nome do paciente para exibição
    $paciente = $pacienteModel->getById($formData['paciente_id']);
    if ($paciente) {
        $formData['paciente_nome'] = $paciente['nome'];
    }
}

if (isset($_GET['clinica_id']) && !empty($_GET['clinica_id'])) {
    $formData['clinica_id'] = (int) $_GET['clinica_id'];
}

if (isset($_GET['especialidade_id']) && !empty($_GET['especialidade_id'])) {
    $formData['especialidade_id'] = (int) $_GET['especialidade_id'];
}

// Se tiver procedimento selecionado
if (isset($_GET['procedimento_id']) && !empty($_GET['procedimento_id'])) {
    $formData['procedimento_id'] = (int) $_GET['procedimento_id'];
}

// Busca procedimentos se tiver especialidade selecionada
if (isset($formData['especialidade_id']) && !empty($formData['especialidade_id'])) {
    try {
        $procedimentos = $procedimentoModel->getByEspecialidade($formData['especialidade_id']);
    } catch (Exception $e) {
        $procedimentos = [];
        error_log("Erro ao buscar procedimentos: " . $e->getMessage());
    }
} else {
    $procedimentos = [];
}

// Busca dados para os selects
$pacientes = $pacienteModel->getAll(['status' => 1], 'nome');
$clinicas = $clinicaModel->getAll(['status' => 1], 'nome');
$especialidades = $especialidadeModel->getAll(['status' => 1], 'nome');

// Status de agendamento disponíveis
$statusAgendamento = [
    'agendado' => 'Agendado',
    'confirmado' => 'Confirmado',
    'realizado' => 'Realizado',
    'cancelado' => 'Cancelado'
];

// Inclui o template de formulário
include AGENDAMENTOS_TEMPLATE_PATH . '/formulario.php';