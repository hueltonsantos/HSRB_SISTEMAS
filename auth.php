<?php
/**
 * Arquivo de autenticação
 * Inclua este arquivo em todas as páginas que requerem autenticação
 */
require_once 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Salvar a URL atual para redirecionar após o login (opcional)
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

/**
 * Função para verificar se o usuário tem permissão para acessar determinada página
 * @param array $niveis_permitidos Array com os níveis de acesso que podem acessar a página
 * @param string $redirect_url URL para redirecionar em caso de acesso negado
 * @return bool Retorna true se o usuário tem permissão
 */
function verificar_acesso($niveis_permitidos, $redirect_url = 'acesso_negado.php') {
    if (!isset($_SESSION['usuario_nivel']) || !in_array($_SESSION['usuario_nivel'], $niveis_permitidos)) {
        header('Location: ' . $redirect_url);
        exit;
    }
    return true;
}