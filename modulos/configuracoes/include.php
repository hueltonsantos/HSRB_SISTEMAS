<?php
// Arquivo de inclusão do módulo de configurações
define('CONFIGURACOES_PATH', __DIR__);
define('CONFIGURACOES_TEMPLATE_PATH', CONFIGURACOES_PATH . '/templates/');
define('CONFIGURACOES_CONTROLLER_PATH', CONFIGURACOES_PATH . '/controllers/');
define('CONFIGURACOES_MODEL_PATH', CONFIGURACOES_PATH . '/models/');

// Carrega o modelo de configurações
require_once CONFIGURACOES_MODEL_PATH . 'ConfiguracaoModel.php';

// Função de processamento de ações do módulo
function configuracoesProcessAction($action) {
    // Buffer de saída para capturar o conteúdo
    ob_start();
    
    // Define o arquivo do controlador
    $controller_file = CONFIGURACOES_CONTROLLER_PATH . $action . '.php';
    
    // Verifica se o arquivo existe
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        // Se o controller não existir, carrega o index como padrão
        require_once CONFIGURACOES_CONTROLLER_PATH . 'index.php';
    }
    
    // Retorna o conteúdo capturado
    return ob_get_clean();
}