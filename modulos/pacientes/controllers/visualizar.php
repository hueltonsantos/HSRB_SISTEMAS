<?php
/**
 * Controlador para visualização de paciente
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do paciente não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Obtém o ID do paciente
$id = (int) $_GET['id'];

// Instancia o modelo de pacientes
$pacienteModel = new PacienteModel();

// Busca os dados do paciente
$paciente = $pacienteModel->getById($id);

// Verifica se o paciente existe
if (!$paciente) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Paciente não encontrado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Formata a data de nascimento para exibição
if (isset($paciente['data_nascimento']) && !empty($paciente['data_nascimento'])) {
    $paciente['data_nascimento_formatada'] = $pacienteModel->formatDateForDisplay($paciente['data_nascimento']);
}

// Formata a data de cadastro para exibição
if (isset($paciente['data_cadastro']) && !empty($paciente['data_cadastro'])) {
    $paciente['data_cadastro_formatada'] = $pacienteModel->formatDateForDisplay($paciente['data_cadastro'], true);
}

// Formata a data de última atualização para exibição
if (isset($paciente['ultima_atualizacao']) && !empty($paciente['ultima_atualizacao'])) {
    $paciente['ultima_atualizacao_formatada'] = $pacienteModel->formatDateForDisplay($paciente['ultima_atualizacao'], true);
}

// Define o status como texto
$paciente['status_texto'] = $paciente['status'] == 1 ? 'Ativo' : 'Inativo';

// Inclui o template de visualização
include PACIENTES_TEMPLATE_PATH . '/visualizar.php';