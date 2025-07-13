<?php
/**
 * Controlador para salvar clínicas
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Instancia o modelo de clínicas
$clinicaModel = new ClinicaModel();

// Obtém os dados do formulário
$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
$data = [
    'nome' => isset($_POST['nome']) ? trim($_POST['nome']) : '',
    'razao_social' => isset($_POST['razao_social']) ? trim($_POST['razao_social']) : '',
    'cnpj' => isset($_POST['cnpj']) ? trim($_POST['cnpj']) : '',
    'responsavel' => isset($_POST['responsavel']) ? trim($_POST['responsavel']) : '',
    'endereco' => isset($_POST['endereco']) ? trim($_POST['endereco']) : '',
    'numero' => isset($_POST['numero']) ? trim($_POST['numero']) : '',
    'complemento' => isset($_POST['complemento']) ? trim($_POST['complemento']) : '',
    'bairro' => isset($_POST['bairro']) ? trim($_POST['bairro']) : '',
    'cidade' => isset($_POST['cidade']) ? trim($_POST['cidade']) : '',
    'estado' => isset($_POST['estado']) ? trim($_POST['estado']) : '',
    'cep' => isset($_POST['cep']) ? trim($_POST['cep']) : '',
    'telefone' => isset($_POST['telefone']) ? trim($_POST['telefone']) : '',
    'celular' => isset($_POST['celular']) ? trim($_POST['celular']) : '',
    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
    'site' => isset($_POST['site']) ? trim($_POST['site']) : '',
    'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1
];

// Se for uma edição, adiciona o ID aos dados
if ($id) {
    $data['id'] = $id;
}

// Salva os dados
$result = $clinicaModel->saveClinica($data);

// Prepara a mensagem para exibição
if ($result['success']) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Clínica salva com sucesso!'
    ];
    
    // Redireciona para a visualização
    header('Location: index.php?module=clinicas&action=view&id=' . $result['id']);
    exit;
} else {
    // Em caso de erro, mantém os dados para corrigir
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar clínica: ' . $result['message']
    ];
    
    // Redireciona de volta para o formulário
    if ($id) {
        header('Location: index.php?module=clinicas&action=edit&id=' . $id);
    } else {
        header('Location: index.php?module=clinicas&action=new');
    }
    exit;
}