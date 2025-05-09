<?php
/**
 * Controlador para salvar agendamentos
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// Obtém os dados do formulário
$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
$data = [
    'paciente_id' => isset($_POST['paciente_id']) ? (int) $_POST['paciente_id'] : null,
    'clinica_id' => isset($_POST['clinica_id']) ? (int) $_POST['clinica_id'] : null,
    'especialidade_id' => isset($_POST['especialidade_id']) ? (int) $_POST['especialidade_id'] : null,
    'data_consulta' => isset($_POST['data_consulta']) ? trim($_POST['data_consulta']) : null,
    'hora_consulta' => isset($_POST['hora_consulta']) ? trim($_POST['hora_consulta']) : null,
    'status_agendamento' => isset($_POST['status_agendamento']) ? trim($_POST['status_agendamento']) : 'agendado',
    'observacoes' => isset($_POST['observacoes']) ? trim($_POST['observacoes']) : null
];

// Se for uma edição, adiciona o ID aos dados
if ($id) {
    $data['id'] = $id;
}

// Salva os dados
$result = $agendamentoModel->saveAgendamento($data);

// Prepara a mensagem para exibição
if ($result['success']) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Agendamento salvo com sucesso!'
    ];
    
    // Redireciona para a visualização
    header('Location: index.php?module=agendamentos&action=view&id=' . $result['id']);
    exit;
} else {
    // Em caso de erro, mantém os dados para corrigir
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_errors'] = $result['errors'];
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar agendamento: ' . $result['message']
    ];
    
    // Redireciona de volta para o formulário
    if ($id) {
        header('Location: index.php?module=agendamentos&action=edit&id=' . $id);
    } else {
        header('Location: index.php?module=agendamentos&action=new');
    }
    exit;
}