<?php
/**
 * Listar Profissionais - Minha Clinica
 */

if (!hasPermission('master_profissionais')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();

// Filtros
$especialidadeId = isset($_GET['especialidade_id']) ? (int)$_GET['especialidade_id'] : null;

$profissionais = $model->getProfissionais($especialidadeId);
$especialidades = $model->getEspecialidades();

$pageTitle = 'Profissionais - Minha Clinica';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/profissionais/listar.php';
