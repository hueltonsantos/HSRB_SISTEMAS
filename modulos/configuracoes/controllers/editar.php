<?php
// verifica permissão
// verificar_acesso(['admin']);

$configuracaoModel = new ConfiguracaoModel();

// Se for POST, processar salvamento
// (Na verdade, form action geralmente aponta para salvar.php, mas vamos ver)
// Se for só exibir:

$configuracoes = $configuracaoModel->listar();

// Agrupa as configurações por tipo (para compatibilidade com formulario.php)
$configPorTipo = [];
foreach ($configuracoes as $config) {
    $tipo = $config['tipo'];
    if (!isset($configPorTipo[$tipo])) {
        $configPorTipo[$tipo] = [];
    }
    $configPorTipo[$tipo][] = $config;
}

require_once CONFIGURACOES_TEMPLATE_PATH . 'formulario.php';
?>