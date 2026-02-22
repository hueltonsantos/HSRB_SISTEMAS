<?php
verificar_acesso('price_manage');

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    $model = new PrecoModel();
    $model->delete($id);
}

header('Location: index.php?module=tabela_precos');
exit;
