<?php
/**
 * Listar Convênios - Minha Clinica
 */

if (!hasPermission('minha_clinica_ver')) { // Ajustar permissão conforme necessário
    header('Location: acesso_negado.php');
    exit;
}

$model = new ConveniosModel();
$convenios = $model->getAll([], 'nome_fantasia', 'ASC');

$pageTitle = 'Convênios - Minha Clínica';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/convenios/listar.php';
