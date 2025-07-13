<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Controlador para exclusão de agendamento
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Verifica se o ID foi informado
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do agendamento não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Obtém o ID do agendamento
$id = (int) $_POST['id'];

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// Verifica se o agendamento existe
$agendamento = $agendamentoModel->getById($id);
if (!$agendamento) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Agendamento não encontrado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Alternativa à exclusão física: marcar como cancelado
if (isset($_POST['tipo_exclusao']) && $_POST['tipo_exclusao'] === 'cancelar') {
    $result = $agendamentoModel->atualizarStatus($id, 'cancelado');
    
    if ($result) {
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Agendamento cancelado com sucesso!'
        ];
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Erro ao cancelar agendamento'
        ];
    }
} else {
    // Exclusão física
    try {
        $result = $agendamentoModel->delete($id);
        
        if ($result) {
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => 'Agendamento excluído com sucesso!'
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
            'texto' => 'Erro ao excluir agendamento: ' . $e->getMessage()
        ];
    }
}

// Redireciona para a listagem
header('Location: index.php?module=agendamentos&action=list');
exit;