<?php
/**
 * Visualizar Agendamento - Minha Clinica
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

$pageTitle = 'Visualizar Agendamento #' . $id;
require_once MINHA_CLINICA_TEMPLATES_PATH . '/agendamentos/ver.php';
