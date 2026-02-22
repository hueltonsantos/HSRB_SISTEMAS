<?php
require_once 'auth.php';
verificar_acesso(['appointment_view', 'appointment_create']);

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// Inclui modelos relacionados
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';
require_once MODULES_PATH . '/especialidades/models/ValorProcedimentoModel.php';

$pacienteModel = new PacienteModel();
$clinicaModel = new ClinicaModel();
$especialidadeModel = new EspecialidadeModel();
$procedimentoModel = new ValorProcedimentoModel();

// Obtém o ID do agendamento
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Agendamento não encontrado.'];
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Busca o agendamento completo
$agendamento = $agendamentoModel->getAgendamentoCompleto($id);

if (!$agendamento) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Agendamento não encontrado.'];
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Verifica se há dados de formulário na sessão (em caso de erro de validação)
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);

// Se não há dados de sessão (primeira abertura), usa os dados do agendamento
if (empty($formData)) {
    $formData = [
        'id' => $agendamento['id'],
        'paciente_id' => $agendamento['paciente_id'],
        'paciente_nome' => $agendamento['paciente_nome'],
        'clinica_id' => $agendamento['clinica_id'],
        'especialidade_id' => $agendamento['especialidade_id'],
        'data_consulta' => $agendamento['data_consulta_formatada'],
        'hora_consulta' => $agendamento['hora_consulta'],
        'status_agendamento' => $agendamento['status_agendamento'],
        'observacoes' => $agendamento['observacoes'],
        'valor_total' => $agendamento['valor_total'],
        'forma_pagamento' => $agendamento['forma_pagamento']
    ];
}

// Define o título da página
$pageTitle = "Editar Agendamento #" . $id;

// Busca especialidades da clínica selecionada
$especialidades = [];
if (!empty($formData['clinica_id'])) {
    $sql = "SELECT e.id, e.nome
            FROM especialidades e
            INNER JOIN especialidades_clinicas ec ON e.id = ec.especialidade_id
            WHERE ec.clinica_id = ? AND e.status = 1 AND ec.status = 1
            ORDER BY e.nome";
    $db = Database::getInstance();
    $especialidades = $db->fetchAll($sql, [$formData['clinica_id']]);
}

// Busca procedimentos se tiver especialidade selecionada
if (!empty($formData['especialidade_id'])) {
    try {
        $procedimentos = $procedimentoModel->getByEspecialidade($formData['especialidade_id']);
    } catch (Exception $e) {
        $procedimentos = [];
    }
} else {
    $procedimentos = [];
}

// Busca dados para os selects
$pacientes = $pacienteModel->getAll(['status' => 1], 'nome');
$clinicas = $clinicaModel->getAll(['status' => 1], 'nome');

// Status de agendamento disponíveis
$statusAgendamento = [
    'agendado' => 'Agendado',
    'confirmado' => 'Confirmado',
    'realizado' => 'Realizado',
    'cancelado' => 'Cancelado'
];

// Inclui o template de formulário (mesmo do new)
include AGENDAMENTOS_TEMPLATE_PATH . '/formulario.php';
