<?php
require_once '../config.php';
require_once '../functions.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

// Inicia o controlador
$controller = new ProcedimentoController($db);

// Processa o formulário
$resultado = $controller->salvar($_POST);

// Retorna o resultado
echo json_encode($resultado);