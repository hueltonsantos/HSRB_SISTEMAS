<?php
/**
 * Editar Agendamento - Minha Clinica
 */

if (!hasPermission('master_agendamentos')) {
    header('Location: acesso_negado.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Agendamento nao encontrado'
    ];
    header('Location: index.php?module=minha_clinica&action=agendamentos');
    exit;
}

$model = new MinhaClinicaModel();
$agendamento = $model->getAgendamento($id);

if (!$agendamento) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Agendamento nao encontrado'
    ];
    header('Location: index.php?module=minha_clinica&action=agendamentos');
    exit;
}

// Dados para o formulario
$especialidades = $model->getEspecialidades(true);
$profissionais = $model->getProfissionais(null, true);
$procedimentos = $model->getProcedimentos($agendamento['especialidade_id'], true);

// Buscar pacientes para select
$db = Database::getInstance();
$pacientes = $db->fetchAll("SELECT id, nome, celular FROM pacientes WHERE status = 1 ORDER BY nome ASC LIMIT 500");

$pageTitle = 'Editar Agendamento #' . $id;
$formAction = 'index.php?module=minha_clinica&action=salvar_agendamento';
$isEdit = true;

require_once MINHA_CLINICA_TEMPLATES_PATH . '/agendamentos/form.php';
