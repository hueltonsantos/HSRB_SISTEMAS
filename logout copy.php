<?php
/**
 * Sistema de Encaminhamento Clínico
 * Arquivo de logout - encerra a sessão do usuário
 */

// Inicia a sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destrói o cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Registra o logout no log do sistema (opcional)
// logActivity('Logout', 'Usuário realizou logout do sistema');

// Redireciona para a página de login
header("Location: index.php");
exit;
?>