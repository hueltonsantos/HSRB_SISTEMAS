<?php
/**
 * Relatório de Inadimplência - Minha Clínica
 * Guias Faturadas que excederam o prazo de pagamento do convênio
 */

if (!hasPermission('minha_clinica_financeiro')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();

$filtroConvenio = $_GET['convenio_id'] ?? '';
$diasAtraso = $_GET['dias_atraso'] ?? 0; // Mostrar guias vencidas há X dias

// Listar Convênios
$convenios = $db->fetchAll("SELECT id, nome_fantasia FROM master_convenios WHERE ativo = 1 ORDER BY nome_fantasia");

// Query Principal
// Considera inadimplente:
// 1. Status = 'faturada' (enviada para cobrança)
// 2. Data Emissão + Prazo Recebimento < Data Atual
$sql = "SELECT g.*, 
               pac.nome as paciente_nome,
               conv.nome_fantasia as convenio_nome,
               conv.prazo_recebimento_dias,
               prof.nome as profissional_nome,
               DATEDIFF(NOW(), DATE_ADD(g.data_emissao, INTERVAL COALESCE(conv.prazo_recebimento_dias, 30) DAY)) as dias_atrasado,
               DATE_ADD(g.data_emissao, INTERVAL COALESCE(conv.prazo_recebimento_dias, 30) DAY) as data_prevista_pagamento
        FROM master_guias g
        JOIN master_convenios conv ON g.convenio_id = conv.id
        JOIN pacientes pac ON g.paciente_id = pac.id
        JOIN master_profissionais prof ON g.profissional_id = prof.id
        WHERE g.status = 'faturada'
        AND DATE_ADD(g.data_emissao, INTERVAL COALESCE(conv.prazo_recebimento_dias, 30) DAY) < CURDATE()";

$params = [];

if ($filtroConvenio) {
    $sql .= " AND g.convenio_id = ?";
    $params[] = $filtroConvenio;
}

if ($diasAtraso > 0) {
    $sql .= " AND DATEDIFF(NOW(), DATE_ADD(g.data_emissao, INTERVAL COALESCE(conv.prazo_recebimento_dias, 30) DAY)) >= ?";
    $params[] = $diasAtraso;
}

$sql .= " ORDER BY dias_atrasado DESC";

$guiasVencidas = $db->fetchAll($sql, $params);

$totalVencido = 0;
foreach ($guiasVencidas as $g) {
    $totalVencido += $g['valor_total'] ?? 0;
}

$pageTitle = 'Relatório de Inadimplência (Contas Vencidas)';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/financeiro/inadimplencia.php';
