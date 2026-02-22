<?php
/**
 * Listar Procedimentos - Minha Clinica
 */

if (!hasPermission('master_procedimentos')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();

// Filtros
$especialidadeId = isset($_GET['especialidade_id']) ? (int)$_GET['especialidade_id'] : null;

$procedimentos = $model->getProcedimentos($especialidadeId);
$especialidades = $model->getEspecialidades();

$pageTitle = 'Procedimentos - Minha Clinica';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/procedimentos/listar.php';
