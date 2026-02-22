<?php
/**
 * Deletar Especialidade - Minha Clinica
 */

if (!hasPermission('master_especialidades')) {
    header('Location: acesso_negado.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'ID invalido'
    ];
    header('Location: index.php?module=minha_clinica&action=especialidades');
    exit;
}

$model = new MinhaClinicaModel();
$especialidade = $model->getEspecialidade($id);

if (!$especialidade) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Especialidade nao encontrada'
    ];
    header('Location: index.php?module=minha_clinica&action=especialidades');
    exit;
}

try {
    // Verificar se ha procedimentos vinculados
    $procedimentos = $model->getProcedimentos($id);
    if (!empty($procedimentos)) {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Nao e possivel excluir. Existem procedimentos vinculados a esta especialidade.'
        ];
        header('Location: index.php?module=minha_clinica&action=especialidades');
        exit;
    }

    $model->deletarEspecialidade($id);

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    LogModel::registrar('deletar', 'minha_clinica', "Especialidade master #{$id} ({$especialidade['nome']}) excluida", $id);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Especialidade excluida com sucesso!'
    ];
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao excluir: ' . $e->getMessage()
    ];
}

header('Location: index.php?module=minha_clinica&action=especialidades');
exit;
