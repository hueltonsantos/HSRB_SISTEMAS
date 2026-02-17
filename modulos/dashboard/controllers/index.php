<?php
require_once 'auth.php';
// Dashboard acessível a qualquer usuário autenticado (auth.php já garante login)
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
// Versão corrigida
$agendamentosRecentes = $agendamentoModel->searchAgendamentos([], 20);
// Obtém dados para o gráfico de especialidades
$dadosGrafico = $agendamentoModel->getAgendamentosPorEspecialidade();
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
                                            <td><?php echo isset($agendamento['paciente_nome']) ? $agendamento['paciente_nome'] : 'N/A'; ?></td>
                                            <td><?php echo isset($agendamento['clinica_nome']) ? $agendamento['clinica_nome'] : 'N/A'; ?></td>
                                            <td><?php echo isset($agendamento['especialidade_nome']) ? $agendamento['especialidade_nome'] : 'N/A'; ?></td>
                                            <td>
                                                <?php 
                                                $dataHora = isset($agendamento['data_hora']) ? $agendamento['data_hora'] : '';
                                                echo !empty($dataHora) ? date('d/m/Y H:i', strtotime($dataHora)) : 'Data não definida'; 
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $status = isset($agendamento['status']) ? $agendamento['status'] : '';
                                                $statusClass = '';
                                                
                                                if (!empty($status)) {
                                                    switch ($status) {
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
                                                        default:
                                                            $statusClass = 'secondary';
                                                    }
                                                    ?>
                                                    <span class="badge badge-<?php echo $statusClass; ?>">
                                                        <?php echo ucfirst($status); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge badge-secondary">Não definido</span>
                                                <?php } ?>
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



<!-- Script para gráficos
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Verifica se Chart JS está carregado
    if (typeof Chart !== 'undefined') {
        // Gráfico de especialidades
        var ctx = document.getElementById("especialidadesChart");
        if (ctx) {
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
        }
    } else {
        console.warn("Chart.js não está carregado. Adicione a biblioteca para visualizar os gráficos.");
    }
});
</script> -->

<!-- Script para gráficos -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Verifica se Chart JS está carregado
    if (typeof Chart !== 'undefined') {
        // Dados para o gráfico de especialidades
        var especialidades = <?php echo json_encode($dadosGrafico['especialidades'] ?? []); ?>;
        var totais = <?php echo json_encode($dadosGrafico['totais'] ?? []); ?>;
        var cores = <?php echo json_encode($dadosGrafico['cores'] ?? []); ?>;
        var hoverCores = <?php echo json_encode($dadosGrafico['hoverCores'] ?? []); ?>;
        
        // Apenas renderiza o gráfico se houver dados
        if (especialidades.length > 0) {
            // Gráfico de especialidades
            var ctx = document.getElementById("especialidadesChart");
            if (ctx) {
                var myPieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: especialidades,
                        datasets: [{
                            data: totais,
                            backgroundColor: cores,
                            hoverBackgroundColor: hoverCores,
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
                
                // Atualiza a legenda abaixo do gráfico
                var legendaHTML = '';
                for (var i = 0; i < especialidades.length; i++) {
                    legendaHTML += '<span class="mr-2">';
                    legendaHTML += '<i class="fas fa-circle" style="color: ' + cores[i] + '"></i> ';
                    legendaHTML += especialidades[i];
                    legendaHTML += '</span>';
                }
                document.querySelector('.mt-4.text-center.small').innerHTML = legendaHTML;
            }
        } else {
            // Mostra uma mensagem se não houver dados
            document.querySelector('.chart-pie').innerHTML = '<div class="text-center py-4">Não há dados disponíveis</div>';
            document.querySelector('.mt-4.text-center.small').innerHTML = '';
        }
    } else {
        console.warn("Chart.js não está carregado. Adicione a biblioteca para visualizar os gráficos.");
        document.querySelector('.chart-pie').innerHTML = '<div class="text-center py-4">Biblioteca Chart.js não carregada</div>';
    }
});
</script>