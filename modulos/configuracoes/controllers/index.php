<?php
$configuracaoModel = new ConfiguracaoModel();

$configuracoes = $configuracaoModel->listar();

// Agrupa as configurações por tipo
$configPorTipo = [];
foreach ($configuracoes as $config) {
    if (!isset($configPorTipo[$config['tipo']])) {
        $configPorTipo[$config['tipo']] = [];
    }
    $configPorTipo[$config['tipo']][] = $config;
}

require_once CONFIGURACOES_TEMPLATE_PATH . 'index.php';
?>