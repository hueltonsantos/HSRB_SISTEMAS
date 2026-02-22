<?php
require_once 'auth.php';
verificar_acesso('caixa_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=caixa&action=listar');
    exit;
}

$caixaModel = new CaixaModel();

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

// Limpar valor
$valor = isset($_POST['valor']) ? $_POST['valor'] : 0;
if (is_string($valor)) {
    $valor = str_replace(['R$', ' '], '', $valor);
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
    }
}
$valor = floatval($valor);

$data = [
    'data' => isset($_POST['data']) ? trim($_POST['data']) : date('d/m/Y'),
    'tipo' => isset($_POST['tipo']) ? trim($_POST['tipo']) : 'entrada',
    'categoria' => isset($_POST['categoria']) ? trim($_POST['categoria']) : null,
    'descricao' => isset($_POST['descricao']) ? trim($_POST['descricao']) : '',
    'valor' => (float)$valor,
    'forma_pagamento' => isset($_POST['forma_pagamento']) ? trim($_POST['forma_pagamento']) : '',
    'paciente_id' => !empty($_POST['paciente_id']) ? (int)$_POST['paciente_id'] : null,
    'clinica_id' => !empty($_POST['clinica_id']) ? (int)$_POST['clinica_id'] : null,
    'agendamento_id' => !empty($_POST['agendamento_id']) ? (int)$_POST['agendamento_id'] : null,
    'usuario_id' => $_SESSION['usuario_id']
];

if ($id) {
    $data['id'] = $id;
}

$result = $caixaModel->salvarLancamento($data);

if ($result['success']) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Lançamento salvo com sucesso!'
    ];
    header('Location: index.php?module=caixa&action=listar');
    exit;
} else {
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar lançamento: ' . $result['message']
    ];
    header('Location: index.php?module=caixa&action=novo_lancamento');
    exit;
}
