<?php
require_once 'auth.php';
verificar_acesso(['admin']);


/**
 * Módulo de Usuários
 * Arquivo principal que será incluído pelo index.php
 */

// Verificar se o usuário tem permissão de admin (exceto para ação de perfil)
$action = isset($_GET['action']) ? $_GET['action'] : 'listar';
if ($action != 'perfil' && $_SESSION['usuario_nivel'] != 'admin') {
    echo '<div class="alert alert-danger">Você não tem permissão para acessar esta página.</div>';
    exit;
}

// Incluir o controlador
require_once 'modulos/usuarios/controllers/usuario_controller.php';
$controller = new UsuarioController();

// Executar a ação solicitada
switch ($action) {
    case 'listar':
        $controller->listar();
        break;
        
    case 'novo':
        $controller->novo();
        break;
        
    case 'editar':
        $controller->editar();
        break;
        
    case 'salvar':
        $controller->salvar();
        break;
        
    case 'excluir':
        $controller->excluir();
        break;
        
    case 'perfil':
        // Implementar caso necessário
        break;
        
    default:
        $controller->listar();
        break;
}