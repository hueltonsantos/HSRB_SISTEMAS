<?php
/**
 * Controlador para exclusão de paciente
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Verifica se o ID foi informado
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do paciente não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Obtém o ID do paciente
$id = (int) $_POST['id'];

// Instancia o modelo de pacientes
$pacienteModel = new PacienteModel();

// Verifica o tipo de exclusão
$tipoExclusao = isset($_POST['tipo_exclusao']) ? $_POST['tipo_exclusao'] : 'logica';

try {
    if ($tipoExclusao === 'fisica') {
        // Exclusão física (remove o registro do banco)
        $result = $pacienteModel->delete($id);
        $mensagem = 'Paciente excluído permanentemente com sucesso!';
    } else {
        // Exclusão lógica (apenas marca como inativo)
        $result = $pacienteModel->deactivate($id);
        $mensagem = 'Paciente desativado com sucesso!';
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
        'texto' => 'Erro ao excluir paciente: ' . $e->getMessage()
    ];
}

// Redireciona para a listagem
header('Location: index.php?module=pacientes&action=list');
exit;