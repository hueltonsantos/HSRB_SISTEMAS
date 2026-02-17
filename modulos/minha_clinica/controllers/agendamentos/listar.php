<?php
/**
 * Listar Agendamentos - Minha Clinica
 */

if (!hasPermission('master_agendamentos')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();

// Filtros
$filtros = [
    'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-d'),
    'data_fim' => $_GET['data_fim'] ?? date('Y-m-d', strtotime('+30 days')),
    'especialidade_id' => $_GET['especialidade_id'] ?? '',
    'profissional_id' => $_GET['profissional_id'] ?? '',
    'status' => $_GET['status'] ?? ''
];

// Buscar agendamentos
$agendamentos = $model->getAgendamentos($filtros);

// Dados para filtros
$especialidades = $model->getEspecialidades(true);
$profissionais = $model->getProfissionais(null, true);

// Carregar template
require_once MINHA_CLINICA_TEMPLATES_PATH . '/agendamentos/listar.php';
