<?php
/**
 * Arquivo de configuração do sistema
 * Contém constantes e configurações globais
 */

// Informações de acesso ao banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'clinica_encaminhamento');
define('DB_USER', 'root'); // Altere para o seu usuário do MySQL
define('DB_PASS', ''); // Altere para sua senha do MySQL

// Configurações do sistema
define('SYSTEM_NAME', 'Sistema para clínicas');
define('SYSTEM_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/clinica'); // Altere para o URL do seu site

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
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);