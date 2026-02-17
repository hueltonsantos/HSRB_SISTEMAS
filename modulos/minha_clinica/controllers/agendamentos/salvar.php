<?php
/**
 * Salvar Agendamento - Minha Clinica
 */

if (!hasPermission('master_agendamentos')) {
    header('Location: acesso_negado.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=minha_clinica&action=agendamentos');
    exit;
}

$model = new MinhaClinicaModel();

// Preparar dados
$id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;

// Procedimentos (multiplos)
$procedimentos = isset($_POST['procedimentos']) ? $_POST['procedimentos'] : [];

// Formatar valor total
$valor_total = $_POST['valor_total'] ?? 0;
if (is_string($valor_total)) {
    $valor_total = str_replace('.', '', $valor_total);
    $valor_total = str_replace(',', '.', $valor_total);
}

$data = [
    'id' => $id,
    'paciente_id' => (int)$_POST['paciente_id'],
    'especialidade_id' => (int)$_POST['especialidade_id'],
    'procedimento_id' => !empty($procedimentos) ? (int)$procedimentos[0] : null,
    'profissional_id' => !empty($_POST['profissional_id']) ? (int)$_POST['profissional_id'] : null,
    'data_consulta' => $_POST['data_consulta'],
    'hora_consulta' => $_POST['hora_consulta'] . ':00',
    'valor' => (float)$valor_total,
    'forma_pagamento' => $_POST['forma_pagamento'] ?? null,
    'observacoes' => $_POST['observacoes'] ?? null
];

// Validar campos obrigatorios
$errors = [];
if (empty($data['paciente_id'])) $errors[] = 'Paciente obrigatorio';
if (empty($data['especialidade_id'])) $errors[] = 'Especialidade obrigatoria';
if (empty($data['data_consulta'])) $errors[] = 'Data obrigatoria';
if (empty($data['hora_consulta'])) $errors[] = 'Hora obrigatoria';

if (!empty($errors)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => implode(', ', $errors)
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_agendamento&id=' . $id : 'novo_agendamento'));
    exit;
}

try {
    $agendamentoId = $model->salvarAgendamento($data);

    // Salvar procedimentos (multiplos)
    if (!empty($procedimentos)) {
        $model->salvarAgendamentoProcedimentos($agendamentoId, $procedimentos);
    }

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    $acao = $id ? 'editar' : 'criar';
    $descricao = $id
        ? "Agendamento master #{$agendamentoId} atualizado para {$data['data_consulta']} as {$data['hora_consulta']}"
        : "Novo agendamento master #{$agendamentoId} criado para {$data['data_consulta']} as {$data['hora_consulta']}";
    LogModel::registrar($acao, 'minha_clinica', $descricao, $agendamentoId, null, $data);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Agendamento salvo com sucesso!',
        'popup' => true
    ];

    header('Location: index.php?module=minha_clinica');
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar: ' . $e->getMessage()
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_agendamento&id=' . $id : 'novo_agendamento'));
}

exit;
