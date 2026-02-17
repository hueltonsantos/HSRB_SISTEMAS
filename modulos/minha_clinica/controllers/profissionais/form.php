<?php
/**
 * Formulario Profissional - Minha Clinica
 */

if (!hasPermission('master_profissionais')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();
$profissional = null;
$isEdit = false;

// Se for edicao, carrega dados
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $profissional = $model->getProfissional($id);

    if (!$profissional) {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Profissional nao encontrado'
        ];
        header('Location: index.php?module=minha_clinica&action=profissionais');
        exit;
    }
    $isEdit = true;
    $pageTitle = 'Editar Profissional';
} else {
    $pageTitle = 'Novo Profissional';
}

// Recupera dados do formulario em caso de erro
if (isset($_SESSION['form_data'])) {
    $profissional = array_merge($profissional ?? [], $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}

$especialidades = $model->getEspecialidades(true);
$formAction = 'index.php?module=minha_clinica&action=salvar_profissional';

require_once MINHA_CLINICA_TEMPLATES_PATH . '/profissionais/form.php';
