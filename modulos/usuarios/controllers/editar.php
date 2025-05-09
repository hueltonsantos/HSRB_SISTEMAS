<?php
require_once 'auth.php';
verificar_acesso(['admin']);


$usuarioModel = new UsuarioModel();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    // Redireciona se não tiver ID
    header('Location: index.php?modulo=usuarios&action=listar');
    exit;
}

$usuario = $usuarioModel->buscarPorId($id);

if (!$usuario) {
    // Redireciona se o usuário não existir
    header('Location: index.php?modulo=usuarios&action=listar');
    exit;
}

require_once USUARIOS_TEMPLATE_PATH . 'formulario.php';
?>