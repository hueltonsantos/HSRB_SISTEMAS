<?php
verificar_acesso('report_view');

$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01');
$fim = isset($_GET['fim']) ? $_GET['fim'] : date('Y-m-t');

$relatorioModel = new RelatorioModel();
$dados = $relatorioModel->getDadosFinanceiros($inicio, $fim);

// Calculate totals
$totalReceita = 0;
$totalCusto = 0;
$totalLucro = 0;

foreach ($dados as $d) {
    // If database returned string numbers, cast them.
    $totalReceita += (float)$d['valor_paciente'];
    $totalCusto += (float)$d['valor_repasse'];
    $totalLucro += (float)$d['lucro'];
}

require RELATORIOS_TEMPLATE_PATH . 'financeiro.php';
