<?php
/**
 * Deletar Profissional - Minha Clinica
 */

if (!hasPermission('master_profissionais')) {
    header('Location: acesso_negado.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'ID invalido'
    ];
    header('Location: index.php?module=minha_clinica&action=profissionais');
    exit;
}

$model = new MinhaClinicaModel();
$profissional = $model->getProfissional($id);

if (!$profissional) {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Profissional nao encontrado'
    ];
    header('Location: index.php?module=minha_clinica&action=profissionais');
    exit;
}

try {
    // Verificar se ha agendamentos vinculados
    $db = Database::getInstance();
    $agendamentos = $db->fetchOne(
        "SELECT COUNT(*) as total FROM master_agendamentos WHERE profissional_id = ?",
        [$id]
    );

    if ($agendamentos && $agendamentos['total'] > 0) {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Nao e possivel excluir. Existem agendamentos vinculados a este profissional.'
        ];
        header('Location: index.php?module=minha_clinica&action=profissionais');
        exit;
    }

    $model->deletarProfissional($id);

    // Registrar log
    require_once ROOT_PATH . '/modulos/log/models/LogModel.php';
    LogModel::registrar('deletar', 'minha_clinica', "Profissional master #{$id} ({$profissional['nome']}) excluido", $id);

    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Profissional excluido com sucesso!'
    ];
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao excluir: ' . $e->getMessage()
    ];
}

header('Location: index.php?module=minha_clinica&action=profissionais');
exit;
