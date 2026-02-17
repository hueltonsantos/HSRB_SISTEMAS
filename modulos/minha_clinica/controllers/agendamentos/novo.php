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
];

$titulo = 'Novo Agendamento';
$actionUrl = 'index.php?module=minha_clinica&action=salvar_agendamento';

// Carregar template
require_once MINHA_CLINICA_TEMPLATES_PATH . '/agendamentos/form.php';
