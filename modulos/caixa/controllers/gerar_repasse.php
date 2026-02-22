<?php
require_once 'auth.php';
verificar_acesso('repasse_manage');

$caixaModel = new CaixaModel();

$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);

$clinicas = $caixaModel->getClinicas();

include CAIXA_TEMPLATE_PATH . 'formulario_repasse.php';
