<?php
// Garantir que a sessão está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioModel = new UsuarioModel();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?modulo=usuarios&action=listar');
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validação básica
$erro = '';
if (empty($_POST['nome'])) {
    $erro = 'O nome é obrigatório';
} elseif (empty($_POST['email'])) {
    $erro = 'O e-mail é obrigatório';
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $erro = 'O e-mail informado é inválido';
} elseif (empty($_POST['nivel_acesso'])) {
    $erro = 'O nível de acesso é obrigatório';
} elseif ($id == 0 && empty($_POST['senha'])) {
    $erro = 'A senha é obrigatória para novos usuários';
}

if ($erro) {
    // Se houver erro, volta para o formulário
    $_SESSION['erro'] = $erro;
    if ($id) {
        header('Location: index.php?modulo=usuarios&action=editar&id=' . $id);
    } else {
        header('Location: index.php?modulo=usuarios&action=novo');
    }
    exit;
}

// Prepara os dados
$dados = [
    'nome' => $_POST['nome'],
    'email' => $_POST['email'],
    'nivel_acesso' => $_POST['nivel_acesso'],
    'status' => isset($_POST['status']) ? 1 : 0
];

// Adiciona data_cadastro para novos usuários
if ($id == 0) {
    $dados['data_cadastro'] = date('Y-m-d H:i:s');
}

// Se a senha foi informada, adiciona aos dados
if (!empty($_POST['senha'])) {
    $dados['senha'] = $_POST['senha'];
}

try {
    // Inserção ou atualização
    if ($id) {
        // Atualização
        $resultado = $usuarioModel->atualizar($id, $dados);
        $mensagem = 'Usuário atualizado com sucesso!';
    } else {
        // Inserção
        $resultado = $usuarioModel->inserir($dados);
        $mensagem = 'Usuário cadastrado com sucesso!';
    }

    if ($resultado) {
        $_SESSION['sucesso'] = $mensagem;
    } else {
        $_SESSION['erro'] = 'Erro ao salvar usuário. Tente novamente.';
    }
} catch (Exception $e) {
    $_SESSION['erro'] = 'Erro ao salvar usuário: ' . $e->getMessage();
}

header('Location: index.php?modulo=usuarios&action=listar');
exit;
?>