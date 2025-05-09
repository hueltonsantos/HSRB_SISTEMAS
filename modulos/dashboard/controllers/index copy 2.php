<?php
/**
 * Controlador principal do dashboard
 */

// Inclui modelos para obter estatísticas
require_once MODULES_PATH . '/pacientes/models/PacienteModel.php';
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';
require_once MODULES_PATH . '/agendamentos/models/AgendamentoModel.php';

// Instancia os modelos
$pacienteModel = new PacienteModel();
$clinicaModel = new ClinicaModel();
$especialidadeModel = new EspecialidadeModel();
$agendamentoModel = new AgendamentoModel();

// Obtém estatísticas básicas
$totalPacientes = $pacienteModel->count();
$totalClinicas = $clinicaModel->count();
$totalEspecialidades = $especialidadeModel->count();
$totalAgendamentos = $agendamentoModel->count();

// Obtém agendamentos recentes
$agendamentosRecentes = $agendamentoModel->searchAgendamentos([], 5);

// O HTML do template começa aqui
?>

<div class="container-fluid">
    <!-- Título da página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Linha de Cards -->
    <div class="row">
        <!-- Card de Pacientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">PACIENTES</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPacientes; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Clínicas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">CLÍNICAS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalClinicas; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hospital fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Especialidades -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ESPECIALIDADES</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalEspecialidades; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stethoscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Agendamentos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">AGENDAMENTOS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAgendamentos; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Linha de conteúdo adicional -->
    <div class="row">
        <!-- Agendamentos Recentes -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Agendamentos Recentes</h6>
                </div>
                <div class="card-body">
                    <?php if (count($agendamentosRecentes) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Clínica</th>
                                        <th>Especialidade</th>
                                        <th>Data/Hora</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agendamentosRecentes as $agendamento): ?>
                                        <tr>
                                            <td><?php echo $agendamento['paciente_nome']; ?></td>
                                            <td><?php echo $agendamento['clinica_nome']; ?></td>
                                            <td><?php echo $agendamento['especialidade_nome']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($agendamento['data_hora'])); ?></td>
                                            <td>
                                                <?php 
                                                $statusClass = '';
                                                switch ($agendamento['status']) {
                                                    case 'agendado':
                                                        $statusClass = 'primary';
                                                        break;
                                                    case 'confirmado':
                                                        $statusClass = 'success';
                                                        break;
                                                    case 'cancelado':
                                                        $statusClass = 'danger';
                                                        break;
                                                    case 'realizado':
                                                        $statusClass = 'info';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($agendamento['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Não há agendamentos recentes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Estatísticas por Especialidade -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribuição por Especialidade</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="especialidadesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Cardiologia
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Ortopedia
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Oftalmologia
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para gráficos -->
<script>
    // Gráfico de especialidades
    var ctx = document.getElementById("especialidadesChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Cardiologia", "Ortopedia", "Oftalmologia", "Outras"],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
</script>