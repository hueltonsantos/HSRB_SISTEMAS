<?php
/**
 * Controlador para adicionar procedimentos em lote
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

// Define o título da página
$pageTitle = "Adicionar Procedimentos em Lote - " . $especialidade['nome'];

// Inclui o template
include ESPECIALIDADES_TEMPLATE_PATH . '/lote_procedimentos.php';