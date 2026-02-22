<?php
/**
 * Tabela de Preços por Convênio
 */

if (!hasPermission('minha_clinica_ver')) {
    header('Location: acesso_negado.php');
    exit;
}

$conveniosModel = new ConveniosModel();
$clinicaModel = new MinhaClinicaModel(); // Para pegar procedimentos

// Filtro de Convênio Obrigatório
$convenioId = isset($_GET['convenio_id']) ? (int)$_GET['convenio_id'] : null;

$convenios = $conveniosModel->getAll(['ativo' => 1], 'nome_fantasia');
$procedimentos = $clinicaModel->getProcedimentos(null, true); // Todas as especialidades, apenas ativos

$precosDefinidos = [];
if ($convenioId) {
    $precosRaw = $conveniosModel->getPrecosPorConvenio($convenioId);
    // Indexar por procedimento_id para fácil acesso
    foreach ($precosRaw as $p) {
        $precosDefinidos[$p['procedimento_id']] = $p;
    }
}

$pageTitle = 'Tabela de Preços - Minha Clínica';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/tabela_precos/listar.php';
