<?php
/**
 * Listar Especialidades - Minha Clinica
 */

if (!hasPermission('master_especialidades')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();
$especialidades = $model->getEspecialidades();

$pageTitle = 'Especialidades - Minha Clinica';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/especialidades/listar.php';
