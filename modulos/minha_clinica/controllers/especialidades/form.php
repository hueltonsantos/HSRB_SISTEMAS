<?php
/**
 * Formulario Especialidade - Minha Clinica
 */

if (!hasPermission('master_especialidades')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();
$especialidade = null;
$isEdit = false;

// Se for edicao, carrega dados
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $especialidade = $model->getEspecialidade($id);

    if (!$especialidade) {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Especialidade nao encontrada'
        ];
        header('Location: index.php?module=minha_clinica&action=especialidades');
        exit;
    }
    $isEdit = true;
    $pageTitle = 'Editar Especialidade';
} else {
    $pageTitle = 'Nova Especialidade';
}

// Recupera dados do formulario em caso de erro
if (isset($_SESSION['form_data'])) {
    $especialidade = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
}

$formAction = 'index.php?module=minha_clinica&action=salvar_especialidade';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/especialidades/form.php';
