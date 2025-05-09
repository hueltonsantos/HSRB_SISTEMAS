<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Controlador para atualizar o status de um agendamento
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Verifica se os dados necessários foram informados
if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['status']) || empty($_POST['status'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Dados incompletos para atualização de status'
    ];
    
    // Redireciona para a página anterior
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Obtém os dados do formulário
$id = (int) $_POST['id'];
$status = $_POST['status'];

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// Verifica se o agendamento existe
$agendamento = $agendamentoModel->getById($id);
if (!$agendamento) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Agendamento não encontrado'
    ];
    
    // Redireciona para a página anterior
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Atualiza o status
$result = $agendamentoModel->atualizarStatus($id, $status);

if ($result) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Status do agendamento atualizado com sucesso!'
    ];
} else {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao atualizar status do agendamento'
    ];
}

// Redireciona para a página anterior
if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
    header('Location: ' . $_POST['redirect_url']);
} else {
    header('Location: index.php?module=agendamentos&action=view&id=' . $id);
}
exit;