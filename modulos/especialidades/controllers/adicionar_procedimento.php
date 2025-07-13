<?php
/**
 * Controlador para formulário de adicionar procedimento
 */

// Verifica se o ID da especialidade foi informado
if (!isset($_GET['especialidade_id']) || empty($_GET['especialidade_id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da especialidade não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID da especialidade
$especialidadeId = (int) $_GET['especialidade_id'];

// Instancia o modelo de especialidades
$especialidadeModel = new EspecialidadeModel();

// Busca os dados da especialidade
$especialidade = $especialidadeModel->getById($especialidadeId);

// Verifica se a especialidade existe
if (!$especialidade) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Especialidade não encontrada'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Verifica se há dados de formulário na sessão (em caso de erro)
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [
    'especialidade_id' => $especialidadeId
];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];

// Limpa os dados da sessão
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Adicionar Procedimento - " . $especialidade['nome'];

// Inclui o template de formulário
include ESPECIALIDADES_TEMPLATE_PATH . '/formulario_procedimento.php';