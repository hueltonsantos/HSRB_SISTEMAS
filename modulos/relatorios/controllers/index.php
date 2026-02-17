<?php
verificar_acesso('report_view');
$relatorioModel = new RelatorioModel();
$dashboardStats = $relatorioModel->getDashboardStats();

require RELATORIOS_TEMPLATE_PATH . 'index.php';
