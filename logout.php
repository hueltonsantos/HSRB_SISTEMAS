<?php
session_start();
require_once 'config.php';

// Registrar log de logout antes de destruir a sessão
if (isset($_SESSION['usuario_id'])) {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("INSERT INTO logs_sistema (usuario_id, usuario_nome, acao, modulo, descricao, ip, user_agent, data_hora) VALUES (?, ?, 'logout', 'auth', 'Logout realizado', ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['usuario_id'],
            $_SESSION['usuario_nome'] ?? 'Usuário',
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erros de log
    }
}

session_destroy();
header('Location: login.php');
exit;
