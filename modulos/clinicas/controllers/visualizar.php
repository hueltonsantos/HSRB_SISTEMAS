<?php
/**
 * Controlador para visualização de clínica
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da clínica não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Obtém o ID da clínica
$id = (int) $_GET['id'];

// Instancia o modelo de clínicas
$clinicaModel = new ClinicaModel();

// Busca os dados da clínica
$clinica = $clinicaModel->getById($id);

// Verifica se a clínica existe
if (!$clinica) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Clínica não encontrada'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Formata a data de cadastro para exibição
if (isset($clinica['data_cadastro']) && !empty($clinica['data_cadastro'])) {
    $clinica['data_cadastro_formatada'] = $clinicaModel->formatDateForDisplay($clinica['data_cadastro'], true);
}

// Formata a data de última atualização para exibição
if (isset($clinica['ultima_atualizacao']) && !empty($clinica['ultima_atualizacao'])) {
    $clinica['ultima_atualizacao_formatada'] = $clinicaModel->formatDateForDisplay($clinica['ultima_atualizacao'], true);
}

// Define o status como texto
$clinica['status_texto'] = $clinica['status'] == 1 ? 'Ativa' : 'Inativa';

// Busca as especialidades da clínica
$especialidades = $clinicaModel->getEspecialidades($id);

// Inclui o template de visualização
include CLINICAS_TEMPLATE_PATH . '/visualizar.php';