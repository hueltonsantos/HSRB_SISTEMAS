<?php
verificar_acesso('user_manage');

$usuarioModel = new UsuarioModel();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: index.php?module=usuarios&action=listar');
    exit;
}

// Não permitir excluir o próprio usuário
if ($id == $_SESSION['usuario_id']) {
    $_SESSION['erro'] = 'Não é possível excluir o usuário atual.';
    header('Location: index.php?module=usuarios&action=listar');
    exit;
}

// Buscar dados do usuário antes de excluir (para o log)
$usuarioAntes = $usuarioModel->buscarPorId($id);

$resultado = $usuarioModel->deletar($id);

if ($resultado) {
    $_SESSION['sucesso'] = 'Usuário excluído com sucesso!';

    // Registrar log
    registrarLog('excluir', 'usuarios', "Usuário '{$usuarioAntes['nome']}' excluído", $id, [
        'nome' => $usuarioAntes['nome'],
        'email' => $usuarioAntes['email']
    ], null);
} else {
    $_SESSION['erro'] = 'Erro ao excluir usuário. Tente novamente.';
}

header('Location: index.php?module=usuarios&action=listar');
exit;
