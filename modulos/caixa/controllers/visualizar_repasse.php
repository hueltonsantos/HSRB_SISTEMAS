<?php
require_once 'auth.php';
verificar_acesso('repasse_view');

$caixaModel = new CaixaModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Repasse não encontrado.'];
    header('Location: index.php?module=caixa&action=repasses');
    exit;
}

$repasse = $caixaModel->getRepasseCompleto($id);

if (!$repasse) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Repasse não encontrado.'];
    header('Location: index.php?module=caixa&action=repasses');
    exit;
}

include CAIXA_TEMPLATE_PATH . 'visualizar_repasse.php';
