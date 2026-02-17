<?php
/**
 * Dashboard - Minha Clinica
 */

// Verificar permissao
if (!hasPermission('master_dashboard')) {
    header('Location: acesso_negado.php');
    exit;
}

$model = new MinhaClinicaModel();

// Estatisticas do dia
$estatisticasHoje = $model->getEstatisticasHoje();

// Estatisticas do mes
$estatisticasMes = $model->getEstatisticasMes();

// Proximos agendamentos
$proximosAgendamentos = $model->getAgendamentosProximos(8);

// Especialidades e profissionais para contagem
$especialidades = $model->getEspecialidades(true);
$profissionais = $model->getProfissionais(null, true);

// Carregar template
require_once MINHA_CLINICA_TEMPLATES_PATH . '/dashboard.php';
