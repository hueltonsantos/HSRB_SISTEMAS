<?php
/**
 * Salvar Profissional - Minha Clinica
 */

if (!hasPermission('master_profissionais')) {
    header('Location: acesso_negado.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=minha_clinica&action=profissionais');
    exit;
}

$model = new MinhaClinicaModel();

$id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;

$data = [
    'id' => $id,
    'nome' => trim($_POST['nome'] ?? ''),
    'especialidade_id' => !empty($_POST['especialidade_id']) ? (int)$_POST['especialidade_id'] : null,
    'registro_profissional' => trim($_POST['registro_profissional'] ?? ''),
    'telefone' => trim($_POST['telefone'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'status' => isset($_POST['status']) ? 1 : 0
];

// Validar campos obrigatorios
$errors = [];
if (empty($data['nome'])) {
    $errors[] = 'Nome do profissional e obrigatorio';
}

if (!empty($errors)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => implode(', ', $errors)
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_profissional&id=' . $id : 'novo_profissional'));
    exit;
}

try {
    $profissionalId = $model->salvarProfissional($data);

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    $acao = $id ? 'editar' : 'criar';
    $descricao = $id
        ? "Profissional master #{$profissionalId} ({$data['nome']}) atualizado"
        : "Novo profissional master #{$profissionalId} ({$data['nome']}) criado";
    LogModel::registrar($acao, 'minha_clinica', $descricao, $profissionalId, null, $data);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Profissional salvo com sucesso!'
    ];

    header('Location: index.php?module=minha_clinica&action=profissionais');
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar: ' . $e->getMessage()
    ];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php?module=minha_clinica&action=' . ($id ? 'editar_profissional&id=' . $id : 'novo_profissional'));
}

exit;
