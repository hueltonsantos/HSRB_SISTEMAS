<?php
$configuracaoModel = new ConfiguracaoModel();

$configuracoes = $configuracaoModel->listar();

// Agrupa as configurações por tipo
// Agrupa as configurações para o template
$configs_geral = [];
$configs_horarios = [];
$configs_numericos = [];
$configs_opcoes = [];

foreach ($configuracoes as $config) {
    switch ($config['tipo']) {
        case 'texto':
        case 'arquivo':
            $configs_geral[] = $config;
            break;
        case 'hora':
            $configs_horarios[] = $config;
            break;
        case 'numero':
            $configs_numericos[] = $config;
            break;
        case 'booleano':
        case 'lista':
            $configs_opcoes[] = $config;
            break;
        default:
            $configs_geral[] = $config;
    }
}

require_once CONFIGURACOES_TEMPLATE_PATH . 'index.php';
?>