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



// Verifica se deve mostrar o modal de confirmação para gerar guia
$showGuiaModal = isset($_GET['generate_guia']) && $_GET['generate_guia'] == 1 &&
    isset($_GET['procedimento_id']) && !empty($_GET['procedimento_id']) &&
    isset($_GET['paciente_id']) && !empty($_GET['paciente_id']) &&
    isset($_GET['data_agendamento']) && !empty($_GET['data_agendamento']);

// Parâmetros para gerar a guia
$guiaParams = [];
if ($showGuiaModal) {
    $guiaParams = [
        'procedimento_id' => $_GET['procedimento_id'],
        'paciente_id' => $_GET['paciente_id'],
        'data_agendamento' => $_GET['data_agendamento'],
        'horario_agendamento' => isset($_GET['horario_agendamento']) ? $_GET['horario_agendamento'] : ''
    ];
}


// Inclui o template de visualização
include AGENDAMENTOS_TEMPLATE_PATH . '/visualizar.php';


// Modal de Confirmação para Gerar Guia

if (isset($showGuiaModal) && $showGuiaModal): ?>
<div class="modal fade" id="gerarGuiaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerar Guia de Encaminhamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deseja gerar uma guia de encaminhamento para este agendamento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                <a href="index.php?module=especialidades&action=gerar_guia&procedimento_id=<?php echo $guiaParams['procedimento_id']; ?>&paciente_id=<?php echo $guiaParams['paciente_id']; ?>&data_agendamento=<?php echo $guiaParams['data_agendamento']; ?>&horario_agendamento=<?php echo $guiaParams['horario_agendamento']; ?>" class="btn btn-primary">
                    Sim, gerar guia
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#gerarGuiaModal').modal('show');
    });
</script>
<?php endif; ?>