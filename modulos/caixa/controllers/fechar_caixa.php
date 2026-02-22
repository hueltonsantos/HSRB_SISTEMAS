<?php
require_once 'auth.php';
verificar_acesso('caixa_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$caixaModel = new CaixaModel();

$id = isset($_POST['fechamento_id']) ? (int)$_POST['fechamento_id'] : 0;
$observacoes = isset($_POST['observacoes']) ? trim($_POST['observacoes']) : '';

if ($id) {
    $result = $caixaModel->fecharCaixa($id, $observacoes);

    $_SESSION['mensagem'] = [
        'tipo' => $result['success'] ? 'success' : 'danger',
        'texto' => $result['message']
    ];
} else {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do caixa não informado.'
    ];
}

header('Location: index.php?module=caixa&action=listar');
exit;
