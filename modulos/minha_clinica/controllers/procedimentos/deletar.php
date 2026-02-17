<?php
/**
 * Deletar Procedimento - Minha Clinica
 */

if (!hasPermission('master_procedimentos')) {
    header('Location: acesso_negado.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'ID invalido'
    ];
    header('Location: index.php?module=minha_clinica&action=procedimentos');
    exit;
}

$model = new MinhaClinicaModel();
$procedimento = $model->getProcedimento($id);

if (!$procedimento) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Procedimento nao encontrado'
    ];
    header('Location: index.php?module=minha_clinica&action=procedimentos');
    exit;
}

try {
    // Verificar se ha agendamentos vinculados
    $db = Database::getInstance();
    $agendamentos = $db->fetchOne(
        "SELECT COUNT(*) as total FROM master_agendamentos WHERE procedimento_id = ?",
        [$id]
    );

    if ($agendamentos && $agendamentos['total'] > 0) {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Nao e possivel excluir. Existem agendamentos vinculados a este procedimento.'
        ];
        header('Location: index.php?module=minha_clinica&action=procedimentos');
        exit;
    }

    $model->deletarProcedimento($id);

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    LogModel::registrar('deletar', 'minha_clinica', "Procedimento master #{$id} ({$procedimento['procedimento']}) excluido", $id);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Procedimento excluido com sucesso!'
    ];
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao excluir: ' . $e->getMessage()
    ];
}

header('Location: index.php?module=minha_clinica&action=procedimentos');
exit;
