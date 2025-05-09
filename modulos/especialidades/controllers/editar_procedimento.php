<?php
/**
 * Controlador para formulário de editar procedimento
 */

// Verifica se o ID do procedimento foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do procedimento não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID do procedimento
$procedimentoId = (int) $_GET['id'];

// Instancia o modelo de procedimentos
$procedimentoModel = new ProcedimentoModel();

// Busca os dados do procedimento
$procedimento = $procedimentoModel->getById($procedimentoId);

// Verifica se o procedimento existe
if (!$procedimento) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Procedimento não encontrado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID da especialidade do procedimento
$especialidadeId = $procedimento['especialidade_id'];

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
    'id' => $procedimento['id'],
    'especialidade_id' => $especialidadeId,
    'nome' => $procedimento['nome'],
    'valor' => $procedimento['valor'],
    'status' => $procedimento['status']
];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];

// Limpa os dados da sessão
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Editar Procedimento - " . $procedimento['nome'];

// Inclui o template de formulário
include ESPECIALIDADES_TEMPLATE_PATH . '/formulario_procedimento.php';