<?php
$usuarioModel = new UsuarioModel();

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