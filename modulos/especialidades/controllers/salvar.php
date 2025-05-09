<?php
/**
 * Controlador para salvar especialidades
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Instancia o modelo de especialidades
$especialidadeModel = new EspecialidadeModel();

// Obtém os dados do formulário
$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
$data = [
    'nome' => isset($_POST['nome']) ? trim($_POST['nome']) : '',
    'descricao' => isset($_POST['descricao']) ? trim($_POST['descricao']) : '',
    'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1
];

// Se for uma edição, adiciona o ID aos dados
if ($id) {
    $data['id'] = $id;
}

// Salva os dados
$result = $especialidadeModel->saveEspecialidade($data);

// Prepara a mensagem para exibição
if ($result['success']) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Especialidade salva com sucesso!'
    ];
    
    // Redireciona para a visualização
    header('Location: index.php?module=especialidades&action=view&id=' . $result['id']);
    exit;
} else {
    // Em caso de erro, mantém os dados para corrigir
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar especialidade: ' . $result['message']
    ];
    
    // Redireciona de volta para o formulário
    if ($id) {
        header('Location: index.php?module=especialidades&action=edit&id=' . $id);
    } else {
        header('Location: index.php?module=especialidades&action=new');
    }
    exit;
}