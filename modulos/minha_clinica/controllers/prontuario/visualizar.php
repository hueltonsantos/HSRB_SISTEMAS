<?php
/**
 * Controller: Visualizar Prontuário Completo do Paciente
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['paciente_id']) || empty($_GET['paciente_id'])) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'ID do paciente não informado.'];
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

if (function_exists('hasPermission') && !hasPermission('ver_prontuario') && !hasPermission('painel_profissional')) {
    die('<div style="font-family:sans-serif;text-align:center;padding:40px;color:#c0392b;">
        <h3>Acesso Negado</h3>
        <p>Você não tem permissão para visualizar prontuários.</p>
        <p>Solicite ao administrador a permissão <strong>Ver Prontuário Completo</strong>.</p>
    </div>');
}

$pacienteId = (int) $_GET['paciente_id'];

// Carrega dados do paciente
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
$pacienteModel = new PacienteModel();
$paciente = $pacienteModel->getById($pacienteId);

if (!$paciente) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Paciente não encontrado.'];
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Formata idade
$dataNasc = new DateTime($paciente['data_nascimento']);
$hoje = new DateTime();
$idade = $hoje->diff($dataNasc)->y;

// Filtros opcionais por data
$de  = isset($_GET['de'])  ? $_GET['de']  : '';
$ate = isset($_GET['ate']) ? $_GET['ate'] : '';

// Carrega evoluções
$prontuarioModel = new ProntuarioModel();

if (!empty($de) || !empty($ate)) {
    $evolucoes = $prontuarioModel->getEvolucoesFiltradas($pacienteId, $de, $ate);
} else {
    $evolucoes = $prontuarioModel->getEvolucoesPorPaciente($pacienteId);
}

require_once MINHA_CLINICA_TEMPLATES_PATH . '/prontuario/visualizar.php';
