<?php
$usuarioModel = new UsuarioModel();

$termo = isset($_GET['termo']) ? $_GET['termo'] : '';

if (empty($termo)) {
    echo json_encode([]);
    exit;
}

$filtros = [
    'nome' => $termo,
    'email' => $termo
];

$usuarios = $usuarioModel->listar($filtros);

// Retorna em formato JSON
header('Content-Type: application/json');
echo json_encode($usuarios);
exit;
?>