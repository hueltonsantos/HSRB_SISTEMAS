<?php
verificar_acesso('user_manage');

$usuarioModel = new UsuarioModel();
$perfilModel = new PerfilModel();
$perfis = $perfilModel->getAll(); // Carrega todos os perfis do banco

// Filtros
$filtros = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filtros = [
        'nome' => isset($_GET['nome']) ? $_GET['nome'] : '',
        'email' => isset($_GET['email']) ? $_GET['email'] : '',
        'nivel_acesso' => isset($_GET['nivel_acesso']) ? $_GET['nivel_acesso'] : '',
        'status' => isset($_GET['status']) ? $_GET['status'] : ''
    ];
}

$usuarios = $usuarioModel->listar($filtros);

require_once USUARIOS_TEMPLATE_PATH . 'listar.php';
?>