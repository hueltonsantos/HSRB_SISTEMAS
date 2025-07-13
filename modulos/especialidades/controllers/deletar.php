<?php
/**
 * Controlador para exclusão de especialidade
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Verifica se o ID foi informado
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da especialidade não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID da especialidade
$id = (int) $_POST['id'];

// Instancia o modelo de especialidades
$especialidadeModel = new EspecialidadeModel();

// Verifica se a especialidade existe
$especialidade = $especialidadeModel->getById($id);
if (!$especialidade) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Especialidade não encontrada'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Verifica se a especialidade está sendo usada em clínicas ou tem procedimentos
$hasClinicas = $especialidadeModel->isUsedInClinicas($id);
$hasProcedimentos = $especialidadeModel->hasValoresProcedimentos($id);

// Verifica o tipo de exclusão
$tipoExclusao = isset($_POST['tipo_exclusao']) ? $_POST['tipo_exclusao'] : 'logica';

// Se tem clínicas ou procedimentos vinculados e a exclusão é física, exibe mensagem de erro
if (($hasClinicas || $hasProcedimentos) && $tipoExclusao === 'fisica') {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Não é possível excluir permanentemente a especialidade porque ela possui ' . 
                  ($hasClinicas ? 'clínicas' : '') . 
                  ($hasClinicas && $hasProcedimentos ? ' e ' : '') .
                  ($hasProcedimentos ? 'procedimentos' : '') . 
                  ' vinculados. Remova esses vínculos primeiro ou use a exclusão lógica.'
    ];
    
    // Redireciona para a visualização
    header('Location: index.php?module=especialidades&action=view&id=' . $id);
    exit;
}

try {
    if ($tipoExclusao === 'fisica') {
        // Exclusão física (remove o registro do banco)
        $result = $especialidadeModel->delete($id);
        $mensagem = 'Especialidade excluída permanentemente com sucesso!';
    } else {
        // Exclusão lógica (apenas marca como inativo)
        $result = $especialidadeModel->deactivate($id);
        $mensagem = 'Especialidade desativada com sucesso!';
    }
    
    if ($result) {
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => $mensagem
        ];
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Nenhum registro foi afetado.'
        ];
    }
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao excluir especialidade: ' . $e->getMessage()
    ];
}

// Redireciona para a listagem
header('Location: index.php?module=especialidades&action=list');
exit;