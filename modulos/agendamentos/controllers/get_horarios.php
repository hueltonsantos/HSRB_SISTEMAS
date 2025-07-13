<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Controlador para obter horários disponíveis via AJAX
 */

// Define o tipo de conteúdo como JSON
header('Content-Type: application/json');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([]);
    exit;
}

// Verifica se os dados necessários foram informados
if (!isset($_POST['data_consulta']) || empty($_POST['data_consulta']) ||
    !isset($_POST['clinica_id']) || empty($_POST['clinica_id']) || 
    !isset($_POST['especialidade_id']) || empty($_POST['especialidade_id'])) {
    echo json_encode([]);
    exit;
}

// Obtém os dados do formulário
$dataConsulta = $_POST['data_consulta'];
$clinicaId = (int) $_POST['clinica_id'];
$especialidadeId = (int) $_POST['especialidade_id'];

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// ID do agendamento a ser ignorado (caso seja uma edição)
$agendamentoId = isset($_POST['agendamento_id']) ? (int) $_POST['agendamento_id'] : null;

// Obtém os horários disponíveis
$horariosDisponiveis = $agendamentoModel->getHorariosDisponiveis($dataConsulta, $clinicaId, $especialidadeId);

// Retorna os horários em formato JSON
echo json_encode($horariosDisponiveis);
exit;