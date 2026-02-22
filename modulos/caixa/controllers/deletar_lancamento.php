<?php
require_once 'auth.php';
verificar_acesso('caixa_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id) {
    $caixaModel = new CaixaModel();
    $caixaModel->delete($id);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Lançamento excluído com sucesso!'
    ];
} else {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Lançamento não encontrado.'
    ];
}

header('Location: index.php?module=caixa&action=listar');
exit;
