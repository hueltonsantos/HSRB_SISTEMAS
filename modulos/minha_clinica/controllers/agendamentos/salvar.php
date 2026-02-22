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
$id = isset($_POST['id']) && !empty($_POST['id']) ? (int) $_POST['id'] : null;

// Procedimentos (multiplos)
$procedimentos = isset($_POST['procedimentos']) ? $_POST['procedimentos'] : [];

// Formatar valor total
$valor_total = $_POST['valor_total'] ?? 0;
if (is_string($valor_total)) {
    $valor_total = str_replace(['R$', ' '], '', $valor_total);
    if (strpos($valor_total, ',') !== false) {
        $valor_total = str_replace('.', '', $valor_total);
        $valor_total = str_replace(',', '.', $valor_total);
    }
}
$valor_total = floatval($valor_total);

$data = [
    'id' => $id,
    'paciente_id' => (int) $_POST['paciente_id'],
    'especialidade_id' => (int) $_POST['especialidade_id'],
    'procedimento_id' => !empty($procedimentos) ? (int) $procedimentos[0] : null,
    'profissional_id' => !empty($_POST['profissional_id']) ? (int) $_POST['profissional_id'] : null,
    'data_consulta' => $_POST['data_consulta'],
    'hora_consulta' => $_POST['hora_consulta'] . ':00',
    'valor' => (float) $valor_total,
    'forma_pagamento' => $_POST['forma_pagamento'] ?? null,
    'observacoes' => $_POST['observacoes'] ?? null
];

// Validar campos obrigatorios
$errors = [];
if (empty($data['paciente_id']))
    $errors[] = 'Paciente obrigatorio';
if (empty($data['especialidade_id']))
    $errors[] = 'Especialidade obrigatoria';
if (empty($data['data_consulta']))
    $errors[] = 'Data obrigatoria';
if (empty($data['hora_consulta']))
    $errors[] = 'Hora obrigatoria';

if (!empty($errors)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => implode(', ', $errors)
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_agendamento&id=' . $id : 'novo_agendamento'));
    exit;
}

$data['convenio_id'] = !empty($_POST['convenio_id']) ? (int) $_POST['convenio_id'] : null;
$db = Database::getInstance();
$guiaId = null;

try {
    $agendamentoId = $model->salvarAgendamento($data);

    // Salvar procedimentos (multiplos)
    if (!empty($procedimentos)) {
        $model->salvarAgendamentoProcedimentos($agendamentoId, $procedimentos);
    }

    // Gerar Guia (se informado e se for convênio)
    if ($data['convenio_id'] && !empty($_POST['numero_guia'])) {
        // Verifica se guia ja existe para este agendamento
        $guiaExistente = $db->fetchOne("SELECT id FROM master_guias WHERE agendamento_id = ?", [$agendamentoId]);

        $dadosGuia = [
            'numero_guia' => $_POST['numero_guia'],
            'paciente_id' => $data['paciente_id'],
            'convenio_id' => $data['convenio_id'],
            'profissional_id' => $data['profissional_id'],
            'agendamento_id' => $agendamentoId,
            'status' => 'solicitada',
        ];

        if ($guiaExistente) {
            $db->update('master_guias', $dadosGuia, 'id = ?', [$guiaExistente['id']]);
            $guiaId = $guiaExistente['id'];
        } else {
            $guiaId = $db->insert('master_guias', $dadosGuia);
        }

        // Atualiza agendamento com ID da guia
        $db->update('master_agendamentos', ['guia_id' => $guiaId], 'id = ?', [$agendamentoId]);
    }

    // Gerar Financeiro Previsto (Contas a Receber)
    // OBS: Gera para todos, mas no dashboard separamos o que e Particular (Caixa) do que e Convenio (Faturamento Futuro)
    if ($data['valor'] > 0) {
        $financeiroModel = new FinanceiroModel();

        // Verifica se ja existe financeiro para este agendamento
        $previstoExistente = $db->fetchOne("SELECT id FROM master_financeiro_caixa_previsto WHERE agendamento_id = ?", [$agendamentoId]);

        if (!$previstoExistente) {
            $dadosFin = [
                'descricao' => "Atendimento #" . $agendamentoId . " - " . ($data['convenio_id'] ? "Convenio" : "Particular"),
                'valor' => $data['valor'],
                'data_vencimento' => $data['data_consulta'],
                'agendamento_id' => $agendamentoId,
                'convenio_id' => $data['convenio_id'],
                'guia_id' => $guiaId
            ];
            $financeiroModel->gerarPrevisaoRecebimento($dadosFin);
        }
    }

    // Registrar log
    require_once MODULES_PATH . '/log/models/LogModel.php';
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
