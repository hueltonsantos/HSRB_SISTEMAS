<?php
/**
 * Controlador para visualização de agendamento
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do agendamento não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Obtém o ID do agendamento
$id = (int) $_GET['id'];

// Instancia o modelo de agendamentos
$agendamentoModel = new AgendamentoModel();

// Busca os dados do agendamento com informações relacionadas
$agendamento = $agendamentoModel->getAgendamentoCompleto($id);

// Verifica se o agendamento existe
if (!$agendamento) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Agendamento não encontrado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=agendamentos&action=list');
    exit;
}

// Status de agendamento para exibição e cores
$statusInfo = [
    'agendado' => [
        'texto' => 'Agendado',
        'cor' => 'primary',
        'icone' => 'calendar-check'
    ],
    'confirmado' => [
        'texto' => 'Confirmado',
        'cor' => 'info',
        'icone' => 'calendar-check'
    ],
    'realizado' => [
        'texto' => 'Realizado',
        'cor' => 'success',
        'icone' => 'check-circle'
    ],
    'cancelado' => [
        'texto' => 'Cancelado',
        'cor' => 'danger',
        'icone' => 'calendar-times'
    ]
];

// Define o status atual
$statusAtual = isset($statusInfo[$agendamento['status_agendamento']]) ? 
    $statusInfo[$agendamento['status_agendamento']] : 
    $statusInfo['agendado'];

// Inclui o template de visualização
include AGENDAMENTOS_TEMPLATE_PATH . '/visualizar.php';