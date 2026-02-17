<?php
/**
 * Salvar Procedimento - Minha Clinica
 */

if (!hasPermission('master_procedimentos')) {
    header('Location: acesso_negado.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=minha_clinica&action=procedimentos');
    exit;
}

$model = new MinhaClinicaModel();

$id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;

// Formatar valor
$valor = $_POST['valor'] ?? 0;
if (is_string($valor)) {
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
}

$data = [
    'id' => $id,
    'especialidade_id' => (int)$_POST['especialidade_id'],
    'procedimento' => trim($_POST['procedimento'] ?? ''),
    'valor' => (float)$valor,
    'duracao_minutos' => (int)($_POST['duracao_minutos'] ?? 30),
    'status' => isset($_POST['status']) ? 1 : 0
];

// Validar campos obrigatorios
$errors = [];
if (empty($data['especialidade_id'])) {
    $errors[] = 'Especialidade e obrigatoria';
}
if (empty($data['procedimento'])) {
    $errors[] = 'Nome do procedimento e obrigatorio';
}
if ($data['valor'] <= 0) {
    $errors[] = 'Valor deve ser maior que zero';
}

if (!empty($errors)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => implode(', ', $errors)
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_procedimento&id=' . $id : 'novo_procedimento'));
    exit;
}

try {
    $procedimentoId = $model->salvarProcedimento($data);

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    $acao = $id ? 'editar' : 'criar';
    $descricao = $id
        ? "Procedimento master #{$procedimentoId} ({$data['procedimento']}) atualizado"
        : "Novo procedimento master #{$procedimentoId} ({$data['procedimento']}) criado";
    LogModel::registrar($acao, 'minha_clinica', $descricao, $procedimentoId, null, $data);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Procedimento salvo com sucesso!'
    ];

    header('Location: index.php?module=minha_clinica&action=procedimentos');
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar: ' . $e->getMessage()
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_procedimento&id=' . $id : 'novo_procedimento'));
}

exit;
