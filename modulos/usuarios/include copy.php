<?php
// Arquivo de inclusão do módulo de usuários
require_once __DIR__ . '/models/UsuarioModel.php';

// Definindo o caminho base do módulo
define('USUARIOS_PATH', __DIR__);
define('USUARIOS_TEMPLATE_PATH', USUARIOS_PATH . '/templates/');
define('USUARIOS_CONTROLLER_PATH', USUARIOS_PATH . '/controllers/');

// Routing básico para o módulo
$action = isset($_GET['action']) ? $_GET['action'] : 'listar';

switch ($action) {
    case 'listar':
        require_once USUARIOS_CONTROLLER_PATH . 'listar.php';
        break;
    case 'novo':
        require_once USUARIOS_CONTROLLER_PATH . 'novo.php';
        break;
    case 'editar':
        require_once USUARIOS_CONTROLLER_PATH . 'editar.php';
        break;
    case 'visualizar':
        require_once USUARIOS_CONTROLLER_PATH . 'visualizar.php';
        break;
    case 'salvar':
        require_once USUARIOS_CONTROLLER_PATH . 'salvar.php';
        break;
    case 'deletar':
        require_once USUARIOS_CONTROLLER_PATH . 'deletar.php';
        break;
    case 'buscar':
        require_once USUARIOS_CONTROLLER_PATH . 'buscar.php';
        break;
    default:
        require_once USUARIOS_CONTROLLER_PATH . 'listar.php';
        break;
}
?>