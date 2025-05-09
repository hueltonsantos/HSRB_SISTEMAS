<?php
// Carregar a configuração
require_once 'config.php';

// Dados do formulário para teste
$nome = "Usuário Teste";
$email = "teste@email.com";
$senha = password_hash("123456", PASSWORD_DEFAULT);
$nivel_acesso = "admin";
$status = 1;

try {
    // Conexão direta
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Inserção direta sem o campo data_cadastro
    $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([$nome, $email, $senha, $nivel_acesso, $status]);
    
    if ($resultado) {
        echo "SUCESSO! Usuário inserido com ID: " . $pdo->lastInsertId();
    } else {
        echo "FALHA! Erro ao inserir usuário.";
        print_r($stmt->errorInfo());
    }
} catch (PDOException $e) {
    echo "ERRO DE BANCO: " . $e->getMessage();
}