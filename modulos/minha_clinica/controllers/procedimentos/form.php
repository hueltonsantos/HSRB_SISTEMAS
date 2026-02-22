<?php
/**
 * Formulario Procedimento - Minha Clinica
 */

if (!hasPermission('master_procedimentos')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();
$procedimento = null;
$isEdit = false;

// Se for edicao, carrega dados
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $procedimento = $model->getProcedimento($id);

    if (!$procedimento) {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Procedimento nao encontrado'
        ];
        header('Location: index.php?module=minha_clinica&action=procedimentos');
        exit;
    }
    $isEdit = true;
    $pageTitle = 'Editar Procedimento';
} else {
    $pageTitle = 'Novo Procedimento';
    // Especialidade pre-selecionada
    if (isset($_GET['especialidade_id'])) {
        $procedimento = ['especialidade_id' => (int)$_GET['especialidade_id']];
    }
}

// Recupera dados do formulario em caso de erro
if (isset($_SESSION['form_data'])) {
    $procedimento = array_merge($procedimento ?? [], $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}

$especialidades = $model->getEspecialidades(true);
$formAction = 'index.php?module=minha_clinica&action=salvar_procedimento';

require_once MINHA_CLINICA_TEMPLATES_PATH . '/procedimentos/form.php';
