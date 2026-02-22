<?php
verificar_acesso('user_manage');

$usuarioModel = new UsuarioModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php?module=usuarios&action=listar');
    exit;
}

$usuario = $usuarioModel->buscarPorId($id);

if (!$usuario) {
    $_SESSION['erro'] = "Usuário não encontrado.";
    header('Location: index.php?module=usuarios&action=listar');
    exit;
}

require_once USUARIOS_TEMPLATE_PATH . 'visualizar.php';
?>