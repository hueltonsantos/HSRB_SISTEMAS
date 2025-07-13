<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    
    <!-- Cards de estatísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pacientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPacientes; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clínicas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalClinicas; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hospital fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Especialidades</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalEspecialidades; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stethoscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Agendamentos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAgendamentos; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agendamentos Recentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Agendamentos Recentes</h6>
            <div>
                <a href="index.php?module=agendamentos&action=list" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> Ver Todos taojfpasjfpaosjfpojas
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Clínica</th>
                            <th>Especialidade</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agendamentosRecentes)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhum agendamento encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($agendamentosRecentes as $agendamento): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($agendamento['paciente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($agendamento['clinica_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($agendamento['especialidade_nome']); ?></td>
                                    <td><?php echo $agendamento['data_consulta_formatada']; ?> <?php echo substr($agendamento['hora_consulta'], 0, 5); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        switch($agendamento['status_agendamento']) {
                                            case 'agendado': $statusClass = 'primary'; break;
                                            case 'confirmado': $statusClass = 'info'; break;
                                            case 'realizado': $statusClass = 'success'; break;
                                            case 'cancelado': $statusClass = 'danger'; break;
                                            default: $statusClass = 'secondary';
                                        }
                                        ?>
                                        <span class="badge badge-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($agendamento['status_agendamento']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?module=agendamentos&action=view&id=<?php echo $agendamento['id']; ?>" 
                                            class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>