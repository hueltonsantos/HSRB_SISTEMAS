<?php
verificar_acesso('price_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=tabela_precos');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$procedimento = isset($_POST['procedimento']) ? trim($_POST['procedimento']) : '';
$especialidade_id = isset($_POST['especialidade_id']) ? (int)$_POST['especialidade_id'] : '';
$valor_paciente = isset($_POST['valor_paciente']) ? (float)$_POST['valor_paciente'] : 0.00;
$valor_repasse = isset($_POST['valor_repasse']) ? (float)$_POST['valor_repasse'] : 0.00;
$status = isset($_POST['status']) ? 1 : 0;

if (empty($procedimento) || empty($especialidade_id)) {
    die('Campos obrigatórios faltando.');
}

$model = new PrecoModel();

$data = [
    'procedimento' => $procedimento,
    'especialidade_id' => $especialidade_id,
    'valor_paciente' => $valor_paciente,
    'valor_repasse' => $valor_repasse,
    'status' => $status
];

if ($id) {
    $data['id'] = $id;
    $model->save($data);
} else {
    $model->save($data);
}

header('Location: index.php?module=tabela_precos');
exit;
