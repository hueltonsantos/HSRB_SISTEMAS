<?php
// Arquivo de inclusão do módulo de configurações

// Definindo o caminho base do módulo
define('CONFIGURACOES_PATH', __DIR__);
define('CONFIGURACOES_TEMPLATE_PATH', CONFIGURACOES_PATH . '/templates/');
define('CONFIGURACOES_CONTROLLER_PATH', CONFIGURACOES_PATH . '/controllers/');
define('CONFIGURACOES_MODEL_PATH', CONFIGURACOES_PATH . '/models/');

// Cria as pastas se não existirem
if (!file_exists(CONFIGURACOES_MODEL_PATH)) {
    mkdir(CONFIGURACOES_MODEL_PATH, 0755, true);
}
if (!file_exists(CONFIGURACOES_CONTROLLER_PATH)) {
    mkdir(CONFIGURACOES_CONTROLLER_PATH, 0755, true);
}
if (!file_exists(CONFIGURACOES_TEMPLATE_PATH)) {
    mkdir(CONFIGURACOES_TEMPLATE_PATH, 0755, true);
}

// Carrega o modelo de configurações
if (file_exists(CONFIGURACOES_MODEL_PATH . 'ConfiguracaoModel.php')) {
    require_once CONFIGURACOES_MODEL_PATH . 'ConfiguracaoModel.php';
} else {
    die('Arquivo do modelo de configurações não encontrado. Verifique se o arquivo exists em: ' . CONFIGURACOES_MODEL_PATH . 'ConfiguracaoModel.php');
}

// Routing básico para o módulo
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verifica se o arquivo do controller existe
$controller_file = CONFIGURACOES_CONTROLLER_PATH . $action . '.php';
if (file_exists($controller_file)) {
    require_once $controller_file;
} else {
    // Se o controller não existir, carrega o index como padrão
    require_once CONFIGURACOES_CONTROLLER_PATH . 'index.php';
}
?>