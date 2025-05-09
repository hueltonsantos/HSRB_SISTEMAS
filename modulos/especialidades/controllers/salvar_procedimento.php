<?php
/**
 * Controlador para salvar procedimento
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Instancia o modelo de valores/procedimentos
$valorProcedimentoModel = new ValorProcedimentoModel();

// Obtém os dados do formulário
$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
$especialidadeId = isset($_POST['especialidade_id']) ? (int) $_POST['especialidade_id'] : null;

$data = [
    'especialidade_id' => $especialidadeId,
    'procedimento' => isset($_POST['procedimento']) ? trim($_POST['procedimento']) : '',
    'valor' => isset($_POST['valor']) ? trim($_POST['valor']) : '',
    'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1
];

// Se for uma edição, adiciona o ID aos dados
if ($id) {
    $data['id'] = $id;
}

// Salva os dados
$result = $valorProcedimentoModel->saveValorProcedimento($data);

// Prepara a mensagem para exibição
if ($result['success']) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Procedimento salvo com sucesso!'
    ];
    
    // Redireciona para a lista de procedimentos
    header('Location: index.php?module=especialidades&action=procedimentos&id=' . $especialidadeId);
    exit;
} else {
    // Em caso de erro, mantém os dados para corrigir
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar procedimento: ' . $result['message']
    ];
    
    // Redireciona de volta para o formulário
    if ($id) {
        header('Location: index.php?module=especialidades&action=edit_procedimento&id=' . $id);
    } else {
        header('Location: index.php?module=especialidades&action=add_procedimento&especialidade_id=' . $especialidadeId);
    }
    exit;
}