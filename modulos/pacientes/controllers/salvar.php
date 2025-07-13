<?php
/**
 * Controlador para salvar pacientes
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Instancia o modelo de pacientes
$pacienteModel = new PacienteModel();

// Obtém os dados do formulário
$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
$data = [
    'nome' => isset($_POST['nome']) ? trim($_POST['nome']) : '',
    'data_nascimento' => isset($_POST['data_nascimento']) ? trim($_POST['data_nascimento']) : '',
    'cpf' => isset($_POST['cpf']) ? trim($_POST['cpf']) : '',
    'rg' => isset($_POST['rg']) ? trim($_POST['rg']) : '',
    'sexo' => isset($_POST['sexo']) ? trim($_POST['sexo']) : '',
    'endereco' => isset($_POST['endereco']) ? trim($_POST['endereco']) : '',
    'numero' => isset($_POST['numero']) ? trim($_POST['numero']) : '',
    'complemento' => isset($_POST['complemento']) ? trim($_POST['complemento']) : '',
    'bairro' => isset($_POST['bairro']) ? trim($_POST['bairro']) : '',
    'cidade' => isset($_POST['cidade']) ? trim($_POST['cidade']) : '',
    'estado' => isset($_POST['estado']) ? trim($_POST['estado']) : '',
    'cep' => isset($_POST['cep']) ? trim($_POST['cep']) : '',
    'telefone_fixo' => isset($_POST['telefone_fixo']) ? trim($_POST['telefone_fixo']) : '',
    'celular' => isset($_POST['celular']) ? trim($_POST['celular']) : '',
    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
    'convenio' => isset($_POST['convenio']) ? trim($_POST['convenio']) : '',
    'numero_carteirinha' => isset($_POST['numero_carteirinha']) ? trim($_POST['numero_carteirinha']) : '',
    'observacoes' => isset($_POST['observacoes']) ? trim($_POST['observacoes']) : '',
    'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1
];

// Se for uma edição, adiciona o ID aos dados
if ($id) {
    $data['id'] = $id;
}

// Salva os dados
$result = $pacienteModel->savePaciente($data);

// Prepara a mensagem para exibição
if ($result['success']) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Paciente salvo com sucesso!'
    ];
    
    // Redireciona para a visualização
    header('Location: index.php?module=pacientes&action=view&id=' . $result['id']);
    exit;
} else {
    // Em caso de erro, mantém os dados para corrigir
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar paciente: ' . $result['message']
    ];
    
    // Redireciona de volta para o formulário
    if ($id) {
        header('Location: index.php?module=pacientes&action=edit&id=' . $id);
    } else {
        header('Location: index.php?module=pacientes&action=new');
    }
    exit;
}