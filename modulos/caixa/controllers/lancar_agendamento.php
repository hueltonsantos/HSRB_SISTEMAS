<?php
require_once 'auth.php';
verificar_acesso('caixa_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$caixaModel = new CaixaModel();

$agendamentoId = isset($_POST['agendamento_id']) ? (int)$_POST['agendamento_id'] : 0;
$formaPagamento = isset($_POST['forma_pagamento']) ? trim($_POST['forma_pagamento']) : null;

if (!$agendamentoId) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Agendamento não informado.'];
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

// Verifica se o caixa está aberto
$caixaAberto = $caixaModel->getCaixaAberto();
if (!$caixaAberto) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'O caixa precisa estar aberto para fazer lançamentos.'];
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$result = $caixaModel->lancarAgendamentoNoCaixa($agendamentoId, $formaPagamento);

$_SESSION['mensagem'] = [
    'tipo' => $result['success'] ? 'success' : 'danger',
    'texto' => $result['message']
];

header('Location: index.php?module=caixa&action=listar');
exit;
