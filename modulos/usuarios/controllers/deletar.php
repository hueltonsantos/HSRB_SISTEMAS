<?php
require_once 'auth.php';
verificar_acesso(['admin']);

// Resto do código...
$usuarioModel = new UsuarioModel();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: index.php?modulo=usuarios&action=listar');
    exit;
}

// Pode adicionar uma verificação para não permitir excluir o próprio usuário
if ($id == $_SESSION['usuario']['id']) {
    $_SESSION['erro'] = 'Não é possível excluir o usuário atual.';
    header('Location: index.php?modulo=usuarios&action=listar');
    exit;
}

$resultado = $usuarioModel->deletar($id);

if ($resultado) {
    $_SESSION['sucesso'] = 'Usuário excluído com sucesso!';
} else {
    $_SESSION['erro'] = 'Erro ao excluir usuário. Tente novamente.';
}

header('Location: index.php?modulo=usuarios&action=listar');
exit;
?>