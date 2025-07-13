<?php
require_once 'config.php';

// Defina os dados do administrador
$admin_nome = 'Administrador';
$admin_email = 'hueltonti@gmail.com';
$admin_senha = '21052008///W'; // Altere para uma senha segura!

try {
    // Conexão com o banco
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Criar hash da senha
    $senha_hash = password_hash($admin_senha, PASSWORD_DEFAULT);
    
    // Inserir o administrador
    $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso, status) VALUES (?, ?, ?, 'admin', 1)";
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([$admin_nome, $admin_email, $senha_hash]);
    
    if ($resultado) {
        echo "Administrador criado com sucesso!";
        // Exibir os dados para referência
        echo "<pre>";
        echo "Nome: $admin_nome\n";
        echo "Email: $admin_email\n";
        echo "Senha: $admin_senha\n";
        echo "</pre>";
    } else {
        echo "Erro ao criar administrador.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}