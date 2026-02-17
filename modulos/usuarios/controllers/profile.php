<?php
require_once 'auth.php';
// No specific permission check needed, any logged user can view their profile
// verificar_acesso('user_manage'); 

$usuarioModel = new UsuarioModel();

// Get ID from session
$id = $_SESSION['usuario_id'];

if (!$id) {
    header('Location: index.php?module=dashboard');
    exit;
}

$usuario = $usuarioModel->buscarPorId($id);

if (!$usuario) {
    header('Location: index.php?module=dashboard');
    exit;
}

// Fetch data for dropdowns (read-only mostly, but needed for form rendering)
$db = Database::getInstance();
$perfis = $db->fetchAll("SELECT * FROM perfis WHERE status=1 ORDER BY nome");
$clinicas = $db->fetchAll("SELECT * FROM clinicas_parceiras WHERE status=1 ORDER BY nome");
$supervisores = $db->fetchAll("SELECT * FROM usuarios WHERE status=1 AND id != ? ORDER BY nome", [$id]);

// Flag to indicate we are in "My Profile" mode (optional, for template logic)
$isMyProfile = true;

require_once USUARIOS_TEMPLATE_PATH . 'formulario.php';
?>
