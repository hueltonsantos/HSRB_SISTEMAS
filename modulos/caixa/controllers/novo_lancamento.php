<?php
require_once 'auth.php';
verificar_acesso('caixa_manage');

$caixaModel = new CaixaModel();

// Dados do formulário (em caso de erro)
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);

// Formas de pagamento
$formasPagamento = $caixaModel->getFormasPagamento();

// Caixa aberto
$caixaAberto = $caixaModel->getCaixaAberto();

// Buscar pacientes para o select
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
$pacienteModel = new PacienteModel();
$pacientes = $pacienteModel->getAll(['status' => 1], 'nome');

include CAIXA_TEMPLATE_PATH . 'formulario_lancamento.php';
