<?php
/**
 * Deletar Convênio
 */

if (!hasPermission('minha_clinica_editar')) {
    header('Location: acesso_negado.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    try {
        $model = new ConveniosModel();
        // Soft delete (desativar) ou delete físico? O model tem deactivate?
        // O Model base tem deactivate. Vamos usar deactivate para manter histórico.
        $model->deactivate($id);
        
        $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Convênio desativado com sucesso!'];
    } catch (Exception $e) {
        $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Erro ao excluir: ' . $e->getMessage()];
    }
}

header('Location: index.php?module=minha_clinica&action=convenios');
exit;
