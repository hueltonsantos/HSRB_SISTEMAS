<?php
/**
 * Controller: Imprimir Prontuário (uma ou múltiplas evoluções)
 * Aceita:
 *   ?paciente_id=X          → imprime todas as evoluções do paciente (filtros opcionais: de, ate)
 *   ?ids=1,2,3              → imprime evoluções específicas por ID
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (function_exists('hasPermission') && !hasPermission('ver_prontuario') && !hasPermission('painel_profissional')) {
    die('Acesso negado: sem permissão para imprimir prontuário.');
}

require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
$prontuarioModel = new ProntuarioModel();
$pacienteModel   = new PacienteModel();

$evolucoes = [];
$paciente  = null;
$de  = isset($_GET['de'])  ? $_GET['de']  : '';
$ate = isset($_GET['ate']) ? $_GET['ate'] : '';

if (!empty($_GET['ids'])) {
    // IDs específicos (já inclui dados do paciente na query)
    $rawIds = explode(',', $_GET['ids']);
    $ids = array_filter(array_map('intval', $rawIds));

    if (!empty($ids)) {
        $evolucoes = $prontuarioModel->getEvolucoesByIds($ids);
        if (!empty($evolucoes)) {
            $paciente = $pacienteModel->getById($evolucoes[0]['paciente_id']);
        }
    }
} elseif (!empty($_GET['paciente_id'])) {
    $pacienteId = (int) $_GET['paciente_id'];
    $paciente   = $pacienteModel->getById($pacienteId);

    if (!$paciente) {
        die('Paciente não encontrado.');
    }

    if (!empty($de) || !empty($ate)) {
        $evolucoes = $prontuarioModel->getEvolucoesFiltradas($pacienteId, $de, $ate);
    } else {
        $evolucoes = $prontuarioModel->getEvolucoesPorPaciente($pacienteId);
    }
} else {
    die('Nenhum parâmetro informado.');
}

if (empty($evolucoes)) {
    die('Nenhuma evolução encontrada para imprimir.');
}

// Calcula idade
$idade = '';
if ($paciente && !empty($paciente['data_nascimento'])) {
    $dataNasc = new DateTime($paciente['data_nascimento']);
    $hoje = new DateTime();
    $idade = $hoje->diff($dataNasc)->y . ' anos';
}

require_once MINHA_CLINICA_TEMPLATES_PATH . '/prontuario/imprimir_prontuario.php';
exit;
