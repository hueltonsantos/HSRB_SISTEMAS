<?php
/**
 * Prontuário / Atendimento
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (function_exists('hasPermission') && !hasPermission('prontuario_paciente')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();
$agendamentoId = $_GET['agendamento_id'] ?? null;

if (!$agendamentoId) {
    die("Agendamento não informado.");
}

// 1. Buscar Detalhes do Agendamento + Paciente
$sqlAg = "SELECT a.*, 
                 p.nome as paciente_nome, p.data_nascimento, p.sexo,
                 c.nome_fantasia as convenio_nome
          FROM master_agendamentos a
          JOIN pacientes p ON a.paciente_id = p.id
          LEFT JOIN master_convenios c ON a.convenio_id = c.id
          WHERE a.id = ?";
$agendamento = $db->fetchOne($sqlAg, [$agendamentoId]);

if (!$agendamento) {
    die("Agendamento não encontrado.");
}

// 2. Buscar Histórico de Evoluções
require_once MINHA_CLINICA_PATH . '/models/ProntuarioModel.php';
$prontuarioModel = new ProntuarioModel();
$historico = $prontuarioModel->getEvolucoesPorPaciente($agendamento['paciente_id']);

// 3. Setup da View
$pacienteNome = $agendamento['paciente_nome'];
$idade = date_diff(date_create($agendamento['data_nascimento']), date_create('now'))->y;
$dataNasc = date('d/m/Y', strtotime($agendamento['data_nascimento']));

$pageTitle = 'Atendimento: ' . $pacienteNome;
require_once MINHA_CLINICA_TEMPLATES_PATH . '/prontuario/form.php';
