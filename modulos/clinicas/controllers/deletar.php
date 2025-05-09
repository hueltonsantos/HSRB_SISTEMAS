<?php
/**
 * Controlador para exclusão de clínica
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Verifica se o ID foi informado
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da clínica não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Obtém o ID da clínica
$id = (int) $_POST['id'];

// Instancia o modelo de clínicas
$clinicaModel = new ClinicaModel();

// Verifica se a clínica possui agendamentos ou especialidades vinculadas
// Essa verificação seria melhor feita consultando o banco diretamente
// Mas vamos simplificar usando apenas a verificação de especialidades
$hasEspecialidades = $clinicaModel->hasEspecialidades($id);

// Verifica o tipo de exclusão
$tipoExclusao = isset($_POST['tipo_exclusao']) ? $_POST['tipo_exclusao'] : 'logica';

// Se tem especialidades vinculadas e a exclusão é física, exibe mensagem de erro
if ($hasEspecialidades && $tipoExclusao === 'fisica') {
    $_SESSION['mensagem'] = [
        'tipo' => 'warning',
        'texto' => 'Não é possível excluir permanentemente a clínica porque ela possui especialidades vinculadas. Remova as especialidades primeiro ou use a exclusão lógica.'
    ];
    
    // Redireciona para a visualização
    header('Location: index.php?module=clinicas&action=view&id=' . $id);
    exit;
}

try {
    if ($tipoExclusao === 'fisica') {
        // Exclusão física (remove o registro do banco)
        $result = $clinicaModel->delete($id);
        $mensagem = 'Clínica excluída permanentemente com sucesso!';
    } else {
        // Exclusão lógica (apenas marca como inativo)
        $result = $clinicaModel->deactivate($id);
        $mensagem = 'Clínica desativada com sucesso!';
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
        'texto' => 'Erro ao excluir clínica: ' . $e->getMessage()
    ];
}

// Redireciona para a listagem
header('Location: index.php?module=clinicas&action=list');
exit;