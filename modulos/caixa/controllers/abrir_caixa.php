<?php
require_once 'auth.php';
verificar_acesso('caixa_manage');

$caixaModel = new CaixaModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saldoInicial = isset($_POST['saldo_inicial']) ? $_POST['saldo_inicial'] : '0';
    if (is_string($saldoInicial)) {
        $saldoInicial = str_replace(['R$', ' '], '', $saldoInicial);
        if (strpos($saldoInicial, ',') !== false) {
            $saldoInicial = str_replace('.', '', $saldoInicial);
            $saldoInicial = str_replace(',', '.', $saldoInicial);
        }
    }
    $saldoInicial = floatval($saldoInicial);

    $result = $caixaModel->abrirCaixa(['saldo_inicial' => (float)$saldoInicial]);

    $_SESSION['mensagem'] = [
        'tipo' => $result['success'] ? 'success' : 'danger',
        'texto' => $result['message']
    ];

    header('Location: index.php?module=caixa&action=listar');
    exit;
}

// GET - mostra o caixa aberto ou permite abrir
$caixaAberto = $caixaModel->getCaixaAberto();

include CAIXA_TEMPLATE_PATH . 'listar.php';
