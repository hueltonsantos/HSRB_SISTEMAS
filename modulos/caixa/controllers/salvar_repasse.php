<?php
require_once 'auth.php';
verificar_acesso('repasse_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=caixa&action=repasses');
    exit;
}

$caixaModel = new CaixaModel();

$acao = isset($_POST['acao']) ? $_POST['acao'] : 'gerar';

if ($acao === 'pagamento') {
    // Registrar pagamento de repasse existente
    $id = isset($_POST['repasse_id']) ? (int)$_POST['repasse_id'] : 0;
    $valorPago = isset($_POST['valor_pago']) ? $_POST['valor_pago'] : '0';
    if (is_string($valorPago)) {
        $valorPago = str_replace(['R$', ' '], '', $valorPago);
        if (strpos($valorPago, ',') !== false) {
            $valorPago = str_replace('.', '', $valorPago);
            $valorPago = str_replace(',', '.', $valorPago);
        }
    }
    $valorPago = floatval($valorPago);

    $result = $caixaModel->registrarPagamentoRepasse($id, [
        'valor_pago' => (float)$valorPago,
        'observacoes' => isset($_POST['observacoes']) ? trim($_POST['observacoes']) : ''
    ]);

    $_SESSION['mensagem'] = [
        'tipo' => $result['success'] ? 'success' : 'danger',
        'texto' => $result['message']
    ];

    header('Location: index.php?module=caixa&action=visualizar_repasse&id=' . $id);
    exit;
} else {
    // Gerar novo repasse
    $clinicaId = isset($_POST['clinica_id']) ? (int)$_POST['clinica_id'] : 0;
    $periodoInicio = isset($_POST['periodo_inicio']) ? trim($_POST['periodo_inicio']) : '';
    $periodoFim = isset($_POST['periodo_fim']) ? trim($_POST['periodo_fim']) : '';

    if (!$clinicaId || !$periodoInicio || !$periodoFim) {
        $_SESSION['form_data'] = $_POST;
        $_SESSION['form_errors'] = ['geral' => 'Todos os campos são obrigatórios'];
        $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Todos os campos são obrigatórios.'];
        header('Location: index.php?module=caixa&action=gerar_repasse');
        exit;
    }

    $result = $caixaModel->gerarRepasse($clinicaId, $periodoInicio, $periodoFim);

    $_SESSION['mensagem'] = [
        'tipo' => $result['success'] ? 'success' : 'danger',
        'texto' => $result['message']
    ];

    if ($result['success']) {
        header('Location: index.php?module=caixa&action=visualizar_repasse&id=' . $result['id']);
    } else {
        $_SESSION['form_data'] = $_POST;
        header('Location: index.php?module=caixa&action=gerar_repasse');
    }
    exit;
}
