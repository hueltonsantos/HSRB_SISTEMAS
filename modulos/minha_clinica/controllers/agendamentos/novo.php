<?php
/**
 * Novo Agendamento - Minha Clinica
 */

if (!hasPermission('master_agendamentos')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();

// Dados para o formulario
$especialidades = $model->getEspecialidades(true);
$profissionais = $model->getProfissionais(null, true);

// Buscar pacientes (usando model existente)
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
$pacienteModel = new PacienteModel();
$pacientes = $pacienteModel->getAll();

<<<<<<< HEAD
// Buscar convenios
require_once MINHA_CLINICA_PATH . '/models/ConveniosModel.php';
$conveniosModel = new ConveniosModel();
$convenios = $conveniosModel->getAll(['ativo' => 1], 'nome_fantasia');

// Restaurar form data se houver erro de validacao
$formData = $_SESSION['form_data'] ?? null;
if ($formData) {
    unset($_SESSION['form_data']);
}

// Agendamento vazio (ou com dados restaurados)
$agendamento = [
    'id' => '',
    'paciente_id' => $formData['paciente_id'] ?? $_GET['paciente_id'] ?? '',
    'especialidade_id' => $formData['especialidade_id'] ?? '',
    'procedimento_id' => $formData['procedimento_id'] ?? '',
    'profissional_id' => $formData['profissional_id'] ?? '',
    'convenio_id' => $formData['convenio_id'] ?? '',
    'data_consulta' => $formData['data_consulta'] ?? $_GET['data'] ?? date('Y-m-d'),
    'hora_consulta' => $formData['hora_consulta'] ?? $_GET['hora'] ?? '',
    'valor' => $formData['valor_total'] ?? '',
    'forma_pagamento' => $formData['forma_pagamento'] ?? '',
    'observacoes' => $formData['observacoes'] ?? ''
=======
// Agendamento vazio
$agendamento = [
    'id' => '',
    'paciente_id' => $_GET['paciente_id'] ?? '',
    'especialidade_id' => '',
    'procedimento_id' => '',
    'profissional_id' => '',
    'data_consulta' => $_GET['data'] ?? date('Y-m-d'),
    'hora_consulta' => $_GET['hora'] ?? '',
    'valor' => '',
    'forma_pagamento' => '',
    'observacoes' => ''
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
];

$titulo = 'Novo Agendamento';
$actionUrl = 'index.php?module=minha_clinica&action=salvar_agendamento';

// Carregar template
require_once MINHA_CLINICA_TEMPLATES_PATH . '/agendamentos/form.php';
