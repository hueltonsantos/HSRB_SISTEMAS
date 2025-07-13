<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Controlador para visualização de agendamentos em calendário
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

// Filtros de busca
$filters = [];

// Filtro por clínica
if (isset($_GET['clinica_id']) && !empty($_GET['clinica_id'])) {
    $filters['clinica_id'] = (int) $_GET['clinica_id'];
}

// Filtro por especialidade
if (isset($_GET['especialidade_id']) && !empty($_GET['especialidade_id'])) {
    $filters['especialidade_id'] = (int) $_GET['especialidade_id'];
}

// Filtro por status do agendamento
if (isset($_GET['status_agendamento']) && !empty($_GET['status_agendamento'])) {
    $filters['status_agendamento'] = $_GET['status_agendamento'];
}

// Busca todos os agendamentos (sem paginação)
$agendamentos = $agendamentoModel->searchAgendamentos($filters);

// Prepara os dados para o calendário (formato JSON)
$eventos = [];

foreach ($agendamentos as $agendamento) {
    // Define a cor com base no status
    $cor = '#3788d8'; // Padrão: azul (agendado)
    
    switch ($agendamento['status_agendamento']) {
        case 'confirmado':
            $cor = '#17a2b8'; // info
            break;
        case 'realizado':
            $cor = '#28a745'; // success
            break;
        case 'cancelado':
            $cor = '#dc3545'; // danger
            break;
    }
    
    // Formata o título com o nome do paciente e especialidade
    $titulo = $agendamento['paciente_nome'] . ' - ' . $agendamento['especialidade_nome'];
    
    // Formata a data e hora para o formato ISO 8601 (exigido pelo FullCalendar)
    $dataHora = $agendamento['data_consulta'] . 'T' . $agendamento['hora_consulta'];
    
    // Calcula o horário de término (assumindo consultas de 30 minutos)
    $horaInicio = new DateTime($dataHora);
    $horaTermino = clone $horaInicio;
    $horaTermino->add(new DateInterval('PT30M')); // 30 minutos
    
    // Adiciona o evento
    $eventos[] = [
        'id' => $agendamento['id'],
        'title' => $titulo,
        'start' => $dataHora,
        'end' => $horaTermino->format('Y-m-d\TH:i:s'),
        'color' => $cor,
        'url' => 'index.php?module=agendamentos&action=view&id=' . $agendamento['id'],
        'extendedProps' => [
            'paciente_id' => $agendamento['paciente_id'],
            'clinica_id' => $agendamento['clinica_id'],
            'clinica_nome' => $agendamento['clinica_nome'],
            'especialidade_id' => $agendamento['especialidade_id'],
            'especialidade_nome' => $agendamento['especialidade_nome'],
            'status' => $agendamento['status_agendamento']
        ]
    ];
}

// Converte os eventos para JSON para uso no JavaScript
$eventosJson = json_encode($eventos);

// Busca dados para os filtros
$clinicas = $clinicaModel->getAll(['status' => 1], 'nome');
$especialidades = $especialidadeModel->getAll(['status' => 1], 'nome');

// Status de agendamento disponíveis
$statusAgendamento = [
    'agendado' => 'Agendado',
    'confirmado' => 'Confirmado',
    'realizado' => 'Realizado',
    'cancelado' => 'Cancelado'
];

// Inclui o template do calendário
include AGENDAMENTOS_TEMPLATE_PATH . '/calendario.php';