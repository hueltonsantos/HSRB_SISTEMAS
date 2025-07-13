<?php
/**
 * Controlador para o formulário de edição de especialidade
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da especialidade não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID da especialidade
$id = (int) $_GET['id'];

// Instancia o modelo de especialidades
$especialidadeModel = new EspecialidadeModel();

// Busca os dados da especialidade
$especialidade = $especialidadeModel->getById($id);

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
if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
} else {
    $formData = $especialidade;
}

// Obtém os erros do formulário, se houver
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Editar Especialidade";

// Inclui o template de formulário
include ESPECIALIDADES_TEMPLATE_PATH . '/formulario.php';