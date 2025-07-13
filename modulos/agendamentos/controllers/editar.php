<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Controlador para listagem de agendamentos
 */

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// Inclui modelos relacionados
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';

$pacienteModel = new PacienteModel();
$clinicaModel = new ClinicaModel();
$especialidadeModel = new EspecialidadeModel();

// Configurações de paginação
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Filtros de busca
$filters = [];

// Filtro por paciente
if (isset($_GET['paciente_id']) && !empty($_GET['paciente_id'])) {
    $filters['paciente_id'] = (int) $_GET['paciente_id'];
}

// Filtro por nome do paciente
if (isset($_GET['paciente_nome']) && !empty($_GET['paciente_nome'])) {
    $filters['paciente_nome'] = $_GET['paciente_nome'];
}

// Filtro por clínica
if (isset($_GET['clinica_id']) && !empty($_GET['clinica_id'])) {
    $filters['clinica_id'] = (int) $_GET['clinica_id'];
}

// Filtro por especialidade
if (isset($_GET['especialidade_id']) && !empty($_GET['especialidade_id'])) {
    $filters['especialidade_id'] = (int) $_GET['especialidade_id'];
}

// Filtro por data da consulta
if (isset($_GET['data_consulta']) && !empty($_GET['data_consulta'])) {
    $filters['data_consulta'] = $_GET['data_consulta'];
}

// Filtro por período
if (isset($_GET['data_inicio']) && !empty($_GET['data_inicio'])) {
    $filters['data_inicio'] = $_GET['data_inicio'];
}
if (isset($_GET['data_fim']) && !empty($_GET['data_fim'])) {
    $filters['data_fim'] = $_GET['data_fim'];
}

// Filtro por status do agendamento
if (isset($_GET['status_agendamento']) && !empty($_GET['status_agendamento'])) {
    $filters['status_agendamento'] = $_GET['status_agendamento'];
}

// Busca os agendamentos com os filtros
$agendamentos = $agendamentoModel->searchAgendamentos($filters, $limit, $offset);

// Conta o total de agendamentos para a paginação
$totalAgendamentos = $agendamentoModel->countAgendamentos($filters);
$totalPages = ceil($totalAgendamentos / $limit);

// Busca dados para os filtros
$pacientes = $pacienteModel->getAll(['status' => 1], 'nome');
$clinicas = $clinicaModel->getAll(['status' => 1], 'nome');
$especialidades = $especialidadeModel->getAll(['status' => 1], 'nome');

// Status de agendamento disponíveis
$statusAgendamento = [
    'agendado' => 'Agendado',
    'confirmado' => 'Confirmado',
    'realizado' => 'Realizado',
    'cancelado' => 'Cancelado'
];

// Inclui o template de listagem
include AGENDAMENTOS_TEMPLATE_PATH . '/listar.php';