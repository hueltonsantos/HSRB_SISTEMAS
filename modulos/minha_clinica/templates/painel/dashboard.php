<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-md mr-2"></i>
            Olá, Dr(a). <?= htmlspecialchars($nomeProfissional) ?>
        </h1>
        <span class="badge badge-primary shadow p-2">
            <i class="fas fa-calendar-day"></i> Hoje: <?= date('d/m/Y') ?>
        </span>
    </div>

    <!-- Resumo Rápido -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pacientes Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($agendamentos) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Atendimentos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Minha Agenda do Dia</h6>
        </div>
        <div class="card-body">
            <?php if (empty($agendamentos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-coffee fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">Nenhum agendamento para hoje.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th width="80">Hora</th>
                                <th>Paciente</th>
                                <th>Procedimentos</th>
                                <th>Convênio</th>
                                <th>Status Guia</th>
                                <th width="150">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $ag): ?>
                                <tr>
                                    <td class="align-middle text-center font-weight-bold">
                                        <?= substr($ag['hora_consulta'], 0, 5) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($ag['paciente_nome']) ?>
                                    </td>
                                    <td class="align-middle small">
                                        <?= htmlspecialchars($ag['procedimentos_lista']) ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($ag['convenio_nome']): ?>
                                            <?= htmlspecialchars($ag['convenio_nome']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Particular</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($ag['status_guia']): ?>
                                            <?php
                                            $badge = 'secondary';
                                            if ($ag['status_guia'] == 'autorizada')
                                                $badge = 'primary';
                                            if ($ag['status_guia'] == 'faturada')
                                                $badge = 'info';
                                            if ($ag['status_guia'] == 'glosada')
                                                $badge = 'danger';
                                            ?>
                                            <span class="badge badge-<?= $badge ?>"><?= strtoupper($ag['status_guia']) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($ag['status'] == 'realizado'): ?>
                                            <a href="index.php?module=minha_clinica&action=prontuario_paciente&agendamento_id=<?= $ag['id'] ?>"
                                                class="btn btn-primary btn-sm shadow-sm btn-block">
                                                <i class="fas fa-check-circle"></i> Ver Prontuário
                                            </a>
                                        <?php elseif ($ag['status'] == 'cancelado'): ?>
                                            <span class="badge badge-danger">Cancelado</span>
                                        <?php else: ?>
                                            <a href="index.php?module=minha_clinica&action=prontuario_paciente&agendamento_id=<?= $ag['id'] ?>"
                                                class="btn btn-success btn-sm shadow-sm btn-block">
                                                <i class="fas fa-stethoscope"></i> Atender
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>