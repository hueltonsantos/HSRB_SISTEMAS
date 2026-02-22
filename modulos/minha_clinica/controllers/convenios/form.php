<?php
/**
 * Formuário de Convênio (Novo/Editar)
 */

if (!hasPermission('minha_clinica_editar')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new ConveniosModel();
$convenio = [];
$actionLabel = 'Novo';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $convenio = $model->getById($id);
    
    if (!$convenio) {
        $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Convênio não encontrado.'];
        header('Location: index.php?module=minha_clinica&action=convenios');
        exit;
    }
    $actionLabel = 'Editar';
}

$pageTitle = $actionLabel . ' Convênio - Minha Clínica';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/convenios/form.php';
