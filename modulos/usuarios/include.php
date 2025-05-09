<?php
require_once 'auth.php';
verificar_acesso(['admin']);
// Arquivo de inclusão do módulo de usuários
define('USUARIOS_PATH', __DIR__);
define('USUARIOS_TEMPLATE_PATH', USUARIOS_PATH . '/templates/');
define('USUARIOS_CONTROLLER_PATH', USUARIOS_PATH . '/controllers/');
define('USUARIOS_MODEL_PATH', USUARIOS_PATH . '/models/');

// Carrega o Model base primeiro (ajuste o caminho conforme necessário)
if (!class_exists('Model')) {
    require_once __DIR__ . '/../../Model.php';
}

// Carrega o modelo de usuários
require_once USUARIOS_MODEL_PATH . 'UsuarioModel.php';

// Função de processamento de ações do módulo
function usuariosProcessAction($action) {
    // Buffer de saída para capturar o conteúdo
    ob_start();
    
    // Define o arquivo do controlador
    $controller_file = USUARIOS_CONTROLLER_PATH . $action . '.php';
    
    // Verifica se o arquivo existe
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        // Se o controller não existir, carrega o listar como padrão
        require_once USUARIOS_CONTROLLER_PATH . 'listar.php';
    }
    
    // Retorna o conteúdo capturado
    return ob_get_clean();
}