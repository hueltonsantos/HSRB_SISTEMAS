<?php
verificar_acesso('report_view');

$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01');
$fim = isset($_GET['fim']) ? $_GET['fim'] : date('Y-m-t');

$relatorioModel = new RelatorioModel();
$dados = $relatorioModel->getDadosOperacionais($inicio, $fim);
$stats = $relatorioModel->getEstatisticasOperacionais($inicio, $fim);

require RELATORIOS_TEMPLATE_PATH . 'operacional.php';
