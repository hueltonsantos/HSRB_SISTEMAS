<?php
/**
 * Dashboard Financeiro - Minha Clínica
 */

if (!hasPermission('minha_clinica_financeiro')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();

$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim = $_GET['data_fim'] ?? date('Y-m-t');

// 1. Totais do Período

// 1.1 Previsto Particular (Caixa a Receber)
$sqlPrevistoPart = "SELECT SUM(valor_previsto) as total FROM master_financeiro_caixa_previsto 
                WHERE data_vencimento BETWEEN ? AND ? AND status != 'cancelado' AND convenio_id IS NULL";
$totalPrevistoParticular = $db->fetchOne($sqlPrevistoPart, [$dataInicio, $dataFim])['total'] ?? 0;

// 1.2 Previsto Convenio (A Faturar)
$sqlPrevistoConv = "SELECT SUM(valor_previsto) as total FROM master_financeiro_caixa_previsto 
                WHERE data_vencimento BETWEEN ? AND ? AND status != 'cancelado' AND convenio_id IS NOT NULL";
$totalPrevistoConvenio = $db->fetchOne($sqlPrevistoConv, [$dataInicio, $dataFim])['total'] ?? 0;

$sqlRealizado = "SELECT SUM(valor_recebido) as total FROM master_financeiro_caixa_realizado 
                 WHERE data_recebimento BETWEEN ? AND ?";
$totalRealizado = $db->fetchOne($sqlRealizado, [$dataInicio, $dataFim])['total'] ?? 0;

// Pendente Particular (Vencido ou a vencer no periodo, que ainda está pendente)
$sqlPendente = "SELECT SUM(valor_previsto) as total FROM master_financeiro_caixa_previsto 
                WHERE data_vencimento BETWEEN ? AND ? AND status = 'pendente' AND convenio_id IS NULL";
$totalPendente = $db->fetchOne($sqlPendente, [$dataInicio, $dataFim])['total'] ?? 0;


// 2. Gráfico: Evolução últimos 6 meses (Previsto x Realizado)
// Para simplificar, vou pegar os dados mês a mês
$dadosGrafico = [];
for ($i = 5; $i >= 0; $i--) {
    $mesIni = date('Y-m-01', strtotime("-$i months"));
    $mesFim = date('Y-m-t', strtotime("-$i months"));
    $label = date('m/Y', strtotime($mesIni));

    $vPrev = $db->fetchOne("SELECT SUM(valor_previsto) as t FROM master_financeiro_caixa_previsto 
                            WHERE data_vencimento BETWEEN ? AND ? AND status != 'cancelado'", [$mesIni, $mesFim])['t'] ?? 0;

    $vReal = $db->fetchOne("SELECT SUM(valor_recebido) as t FROM master_financeiro_caixa_realizado 
                            WHERE data_recebimento BETWEEN ? AND ?", [$mesIni, $mesFim])['t'] ?? 0;

    $dadosGrafico['labels'][] = $label;
    $dadosGrafico['previsto'][] = (float) $vPrev;
    $dadosGrafico['realizado'][] = (float) $vReal;
}


// 3. Últimos Recebimentos
$ultimosRecebimentos = $db->fetchAll("SELECT cr.*, 
                                        COALESCE(pac.nome, 'Não Identificado') as paciente_nome,
                                        COALESCE(conv.nome_fantasia, 'Particular') as convenio_nome
                                      FROM master_financeiro_caixa_realizado cr
                                      LEFT JOIN master_financeiro_caixa_previsto cp ON cr.previsao_id = cp.id
                                      LEFT JOIN master_agendamentos a ON cp.agendamento_id = a.id
                                      LEFT JOIN pacientes pac ON a.paciente_id = pac.id
                                      LEFT JOIN master_convenios conv ON a.convenio_id = conv.id
                                      ORDER BY cr.data_recebimento DESC, cr.id DESC LIMIT 10");

$pageTitle = 'Dashboard Financeiro';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/financeiro/dashboard.php';
