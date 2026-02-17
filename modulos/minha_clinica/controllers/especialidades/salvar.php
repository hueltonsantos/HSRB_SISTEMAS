<?php
/**
 * Salvar Especialidade - Minha Clinica
 */

if (!hasPermission('master_especialidades')) {
    header('Location: acesso_negado.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=minha_clinica&action=especialidades');
    exit;
}

$model = new MinhaClinicaModel();

$id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;

$data = [
    'id' => $id,
    'nome' => trim($_POST['nome'] ?? ''),
    'descricao' => trim($_POST['descricao'] ?? ''),
    'status' => isset($_POST['status']) ? 1 : 0
];

// Validar campos obrigatorios
$errors = [];
if (empty($data['nome'])) {
    $errors[] = 'Nome da especialidade e obrigatorio';
}

if (!empty($errors)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => implode(', ', $errors)
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_especialidade&id=' . $id : 'nova_especialidade'));
    exit;
}

try {
    $especialidadeId = $model->salvarEspecialidade($data);

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    $acao = $id ? 'editar' : 'criar';
    $descricao = $id
        ? "Especialidade master #{$especialidadeId} ({$data['nome']}) atualizada"
        : "Nova especialidade master #{$especialidadeId} ({$data['nome']}) criada";
    LogModel::registrar($acao, 'minha_clinica', $descricao, $especialidadeId, null, $data);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Especialidade salva com sucesso!'
    ];

    header('Location: index.php?module=minha_clinica&action=especialidades');
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar: ' . $e->getMessage()
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_especialidade&id=' . $id : 'nova_especialidade'));
}

exit;
