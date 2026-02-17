<?php
$statusLabels = [
    'agendado' => ['label' => 'Agendado', 'class' => 'primary'],
    'confirmado' => ['label' => 'Confirmado', 'class' => 'info'],
    'realizado' => ['label' => 'Realizado', 'class' => 'success'],
    'cancelado' => ['label' => 'Cancelado', 'class' => 'danger'],
    'faltou' => ['label' => 'Faltou', 'class' => 'warning']
];
$status = $statusLabels[$agendamento['status']] ?? ['label' => 'Indefinido', 'class' => 'secondary'];
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-check mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <div>
            <a href="index.php?module=minha_clinica&action=editar_agendamento&id=<?= $agendamento['id'] ?>"
               class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="index.php?module=minha_clinica&action=agendamentos" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] ?> alert-dismissible fade show">
            <?= $_SESSION['mensagem']['texto'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detalhes do Agendamento</h6>
                    <span class="badge badge-<?= $status['class'] ?> px-3 py-2">
                        <?= $status['label'] ?>
                    </span>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Paciente</label>
                            <p class="mb-0 text-dark">
                                <i class="fas fa-user text-primary mr-1"></i>
                                <strong class="text-dark"><?= !empty($agendamento['paciente_nome']) ? htmlspecialchars($agendamento['paciente_nome']) : '<span class="text-muted">N/A</span>' ?></strong>
                            </p>
                            <?php if (!empty($agendamento['paciente_celular'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($agendamento['paciente_celular']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Data e Hora</label>
                            <p class="mb-0 text-dark">
                                <i class="fas fa-calendar text-primary mr-1"></i>
                                <strong class="text-dark"><?= !empty($agendamento['data_consulta']) ? date('d/m/Y', strtotime($agendamento['data_consulta'])) : '<span class="text-muted">N/A</span>' ?></strong>
                                <span class="mx-2 text-dark">|</span>
                                <i class="fas fa-clock text-primary mr-1"></i>
                                <strong class="text-dark"><?= !empty($agendamento['hora_consulta']) ? date('H:i', strtotime($agendamento['hora_consulta'])) : '<span class="text-muted">N/A</span>' ?></strong>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Especialidade</label>
                            <p class="mb-0 text-dark">
                                <i class="fas fa-stethoscope text-success mr-1"></i>
                                <span class="text-dark"><?= !empty($agendamento['especialidade_nome']) ? htmlspecialchars($agendamento['especialidade_nome']) : '<span class="text-muted">N/A</span>' ?></span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Procedimento</label>
                            <p class="mb-0 text-dark">
                                <i class="fas fa-procedures text-info mr-1"></i>
                                <span class="text-dark"><?= !empty($agendamento['procedimento_nome']) ? htmlspecialchars($agendamento['procedimento_nome']) : '<span class="text-muted">N/A</span>' ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Profissional</label>
                            <p class="mb-0 text-dark">
                                <i class="fas fa-user-md text-primary mr-1"></i>
                                <span class="text-dark"><?= !empty($agendamento['profissional_nome']) ? htmlspecialchars($agendamento['profissional_nome']) : '<span class="text-muted">N/A</span>' ?></span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Forma de Pagamento</label>
                            <p class="mb-0 text-dark">
                                <i class="fas fa-credit-card text-warning mr-1"></i>
                                <span class="text-dark"><?= !empty($agendamento['forma_pagamento']) ? htmlspecialchars($agendamento['forma_pagamento']) : '<span class="text-muted">N/A</span>' ?></span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-600">Valor</label>
                            <p class="mb-0 h4 text-success">
                                R$ <?= number_format($agendamento['valor'] ?? 0, 2, ',', '.') ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($agendamento['observacoes'])): ?>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <label class="font-weight-bold text-gray-600">Observacoes</label>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($agendamento['observacoes'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alterar Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-sm mb-2 mr-2 btn-status"
                                data-status="agendado" data-id="<?= $agendamento['id'] ?>">
                            <i class="fas fa-calendar"></i> Agendado
                        </button>
                        <button type="button" class="btn btn-info btn-sm mb-2 mr-2 btn-status"
                                data-status="confirmado" data-id="<?= $agendamento['id'] ?>">
                            <i class="fas fa-check"></i> Confirmado
                        </button>
                        <button type="button" class="btn btn-success btn-sm mb-2 mr-2 btn-status"
                                data-status="realizado" data-id="<?= $agendamento['id'] ?>">
                            <i class="fas fa-check-double"></i> Realizado
                        </button>
                        <button type="button" class="btn btn-warning btn-sm mb-2 mr-2 btn-status"
                                data-status="faltou" data-id="<?= $agendamento['id'] ?>">
                            <i class="fas fa-user-slash"></i> Faltou
                        </button>
                        <button type="button" class="btn btn-danger btn-sm mb-2 btn-status"
                                data-status="cancelado" data-id="<?= $agendamento['id'] ?>">
                            <i class="fas fa-times"></i> Cancelado
                        </button>
                    </div>
                </div>
            </div>

            <?php if (!empty($agendamento['paciente_celular'])): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">WhatsApp</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                    $celular = preg_replace('/[^0-9]/', '', $agendamento['paciente_celular']);
                    if (strlen($celular) == 10 || strlen($celular) == 11) {
                        $celular = '55' . $celular;
                    }
                    $nomePaciente = !empty($agendamento['paciente_nome']) ? $agendamento['paciente_nome'] : 'Paciente';
                    $dataConsulta = !empty($agendamento['data_consulta']) ? date('d/m/Y', strtotime($agendamento['data_consulta'])) : 'data a definir';
                    $horaConsulta = !empty($agendamento['hora_consulta']) ? date('H:i', strtotime($agendamento['hora_consulta'])) : 'hora a definir';
                    $msg = urlencode("Ola {$nomePaciente}, seu agendamento esta confirmado para {$dataConsulta} as {$horaConsulta}.");
                    ?>
                    <a href="https://wa.me/<?= $celular ?>?text=<?= $msg ?>"
                       target="_blank" class="btn btn-success btn-lg">
                        <i class="fab fa-whatsapp"></i> Enviar Mensagem
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Informacoes do Registro</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Criado em:</strong>
                        <?= date('d/m/Y H:i', strtotime($agendamento['created_at'] ?? 'now')) ?>
                    </small>
                    <?php if (!empty($agendamento['updated_at'])): ?>
                        <br>
                        <small class="text-muted">
                            <strong>Atualizado em:</strong>
                            <?= date('d/m/Y H:i', strtotime($agendamento['updated_at'])) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-status').on('click', function() {
        var status = $(this).data('status');
        var id = $(this).data('id');

        if (confirm('Alterar status para "' + status.toUpperCase() + '"?')) {
            $.ajax({
                url: 'index.php?module=minha_clinica&action=api&api_action=alterar_status',
                method: 'POST',
                data: { id: id, status: status },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        location.reload();
                    } else {
                        alert('Erro: ' + resp.message);
                    }
                },
                error: function() {
                    alert('Erro ao alterar status');
                }
            });
        }
    });
});
</script>
