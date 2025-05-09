<?php
/**
 * Controlador principal do dashboard
 */

// Inclui modelos para obter estatísticas
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';
require_once MODULES_PATH . '/agendamentos/models/AgendamentoModel.php';

// Instancia os modelos
$pacienteModel = new PacienteModel();
$clinicaModel = new ClinicaModel();
$especialidadeModel = new EspecialidadeModel();
$agendamentoModel = new AgendamentoModel();

// Obtém estatísticas básicas
$totalPacientes = $pacienteModel->count();
$totalClinicas = $clinicaModel->count();
$totalEspecialidades = $especialidadeModel->count();
$totalAgendamentos = $agendamentoModel->count();

// Obtém agendamentos recentes
$agendamentosRecentes = $agendamentoModel->searchAgendamentos([], 5);

// Inclui o template
include DASHBOARD_TEMPLATE_PATH . '/index.php';