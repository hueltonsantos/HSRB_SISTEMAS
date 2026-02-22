<?php
require_once 'auth.php';
verificar_acesso('caixa_view');

$caixaModel = new CaixaModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Lançamento não encontrado.'];
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$lancamento = $caixaModel->getLancamentoCompleto($id);

if (!$lancamento) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Lançamento não encontrado.'];
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$formasPagamento = $caixaModel->getFormasPagamento();

// Dados da clínica (configurações)
require_once MODULES_PATH . '/configuracoes/models/ConfiguracaoModel.php';
$configModel = new ConfiguracaoModel();
$nomeClinica = $configModel->obterValor('nome_clinica', 'HSRB Sistemas');
$enderecoClinica = $configModel->obterValor('endereco_clinica', '');
$telefoneClinica = $configModel->obterValor('telefone_clinica', '');

include CAIXA_TEMPLATE_PATH . 'recibo.php';
