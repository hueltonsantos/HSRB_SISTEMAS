<?php
// Verificar se tem popup de sucesso
$showPopup = false;
$popupMsg = '';
if (isset($_SESSION['mensagem']) && isset($_SESSION['mensagem']['popup']) && $_SESSION['mensagem']['popup']) {
    $showPopup = true;
    $popupMsg = $_SESSION['mensagem']['texto'];
    unset($_SESSION['mensagem']);
}
?>

<?php if ($showPopup): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: '<?= addslashes($popupMsg) ?>',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4e73df'
    });
});
</script>
<?php endif; ?>

<div class="container-fluid">
    <!-- Titulo -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clinic-medical text-primary"></i> Minha Clinica
        </h1>
        <a href="index.php?module=minha_clinica&action=novo_agendamento" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Agendamento
        </a>
    </div>

    <!-- Cards de Estatisticas Hoje -->
    <div class="row">
        <!-- Total Agendamentos Hoje -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Agendamentos Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $estatisticasHoje['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Confirmados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $estatisticasHoje['confirmados'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Realizados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Realizados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $estatisticasHoje['realizados'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faturamento Hoje -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Faturamento Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($estatisticasHoje['faturamento'] ?? 0, 2, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards do Mes -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Resumo do Mes (<?= date('F/Y') ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary"><?= $estatisticasMes['total'] ?? 0 ?></h4>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success"><?= $estatisticasMes['realizados'] ?? 0 ?></h4>
                            <small class="text-muted">Realizados</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning">R$ <?= number_format($estatisticasMes['faturamento'] ?? 0, 2, ',', '.') ?></h4>
                            <small class="text-muted">Faturamento</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs"></i> Configuracao
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <a href="index.php?module=minha_clinica&action=especialidades" class="text-decoration-none">
                                <h4 class="text-info"><?= count($especialidades) ?></h4>
                                <small class="text-muted">Especialidades</small>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="index.php?module=minha_clinica&action=procedimentos" class="text-decoration-none">
                                <h4 class="text-info"><?= count($model->getProcedimentos(null, true)) ?></h4>
                                <small class="text-muted">Procedimentos</small>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="index.php?module=minha_clinica&action=profissionais" class="text-decoration-none">
                                <h4 class="text-info"><?= count($profissionais) ?></h4>
                                <small class="text-muted">Profissionais</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proximos Agendamentos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-alt"></i> Proximos Agendamentos
            </h6>
            <a href="index.php?module=minha_clinica&action=agendamentos" class="btn btn-sm btn-outline-primary">
                Ver Todos
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($proximosAgendamentos)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>Nenhum agendamento proximo</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Paciente</th>
                                <th>Especialidade</th>
                                <th>Profissional</th>
                                <th>Status</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proximosAgendamentos as $ag): ?>
                                <tr>
                                    <td>
                                        <strong><?= date('d/m', strtotime($ag['data_consulta'])) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= substr($ag['hora_consulta'], 0, 5) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($ag['paciente_nome']) ?></td>
                                    <td><?= htmlspecialchars($ag['especialidade_nome'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($ag['profissional_nome'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'agendado' => 'warning',
                                            'confirmado' => 'info',
                                            'em_atendimento' => 'primary',
                                            'realizado' => 'success',
                                            'cancelado' => 'danger',
                                            'faltou' => 'secondary'
                                        ];
                                        $class = $statusClass[$ag['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $class ?>"><?= ucfirst($ag['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="index.php?module=minha_clinica&action=ver_agendamento&id=<?= $ag['id'] ?>" class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?module=minha_clinica&action=editar_agendamento&id=<?= $ag['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Acesso Rapido -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="index.php?module=minha_clinica&action=agendamentos" class="card bg-primary text-white shadow text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x mb-2"></i>
                    <h5>Agendamentos</h5>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="index.php?module=minha_clinica&action=especialidades" class="card bg-success text-white shadow text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-stethoscope fa-3x mb-2"></i>
                    <h5>Especialidades</h5>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="index.php?module=minha_clinica&action=procedimentos" class="card bg-info text-white shadow text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-notes-medical fa-3x mb-2"></i>
                    <h5>Procedimentos</h5>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="index.php?module=minha_clinica&action=profissionais" class="card bg-warning text-white shadow text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-user-md fa-3x mb-2"></i>
                    <h5>Profissionais</h5>
                </div>
            </a>
        </div>
    </div>
</div>
