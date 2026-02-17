<?php
/**
 * Controller principal do Dashboard em Tempo Real
 */

$model = new DashboardRealtimeModel();

// Dados para os filtros
$clinicas = $model->getClinicas();
$especialidades = $model->getEspecialidades();
$usuarios = $model->getUsuarios();

// Valores padrão dos filtros (hoje)
$filtros = [
    'data_inicio' => date('Y-m-d'),
    'data_fim' => date('Y-m-d'),
    'clinica_id' => '',
    'especialidade_id' => '',
    'usuario_id' => ''
];

// Carregar template
require_once DASHBOARD_REALTIME_TEMPLATE_PATH . 'index.php';
