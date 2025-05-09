<?php
// Verifica se o acesso é direto
if (!defined('BASEPATH')) exit('Acesso negado');

// Inclui dependências de outros módulos
require_once '../clinicas/controllers/clinica.controller.php';
require_once '../especialidades/controllers/especialidade.controller.php';

// Inclui os arquivos de modelo
require_once 'models/procedimento.model.php';
require_once 'models/procedimento_clinica.model.php';

// Inclui os arquivos de controlador
require_once 'controllers/procedimento.controller.php';
require_once 'controllers/procedimento_clinica.controller.php';

// Função de processamento do módulo - ESTA É A PARTE CRÍTICA QUE ESTAVA FALTANDO
function procedimentosProcessAction($action) {
    global $db;
    
    // Determina qual template carregar com base na ação
    switch ($action) {
        case 'cadastrar':
            ob_start();
            include 'templates/cadastrar_procedimento.php';
            return ob_get_clean();
            
        case 'vincular':
            ob_start();
            include 'templates/vincular_clinica_procedimento.php';
            return ob_get_clean();
            
        case 'list':
        default:
            ob_start();
            include 'templates/listar_procedimentos.php';
            return ob_get_clean();
    }
}

// As rotas já definidas não são necessárias quando se usa a função de processamento acima
// Você pode manter ou remover as linhas abaixo
$router->register('procedimentos', function() {
    include 'templates/listar_procedimentos.php';
});

$router->register('procedimentos/cadastrar', function() {
    include 'templates/cadastrar_procedimento.php';
});

$router->register('procedimentos/vincular', function() {
    include 'templates/vincular_clinica_procedimento.php';
});
?>