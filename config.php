<?php
/**
 * Arquivo de configuração do sistema
 * Contém constantes e configurações globais
 */

// Informações de acesso ao banco de dados
// Suporte para Docker (variáveis de ambiente) ou configuração local
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'clinica_encaminhamento');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Configurações do sistema
define('SYSTEM_NAME', 'Sistema para clínicas');
define('SYSTEM_VERSION', '1.0.0');
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost:8080'); // URL do sistema

// Diretórios do sistema
define('ROOT_PATH', dirname(__FILE__));
define('MODULES_PATH', ROOT_PATH . '/modulos');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Configurações de timezone e locale
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Configurações de exibição de erros (desativar em produção)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); // Menos verboso em produção