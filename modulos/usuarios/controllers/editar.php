<?php
require_once 'auth.php';
verificar_acesso('user_manage');


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

// Fetch data for dropdowns
$db = Database::getInstance();
$perfis = $db->fetchAll("SELECT * FROM perfis WHERE status=1 ORDER BY nome");
$clinicas = $db->fetchAll("SELECT * FROM clinicas_parceiras WHERE status=1 ORDER BY nome");
// Exclude self from supervisors list
$supervisores = $db->fetchAll("SELECT * FROM usuarios WHERE status=1 AND id != ? ORDER BY nome", [$id]);

require_once USUARIOS_TEMPLATE_PATH . 'formulario.php';
?>