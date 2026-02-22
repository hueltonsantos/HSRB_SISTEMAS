<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <form class="form-inline d-print-none" method="GET">
            <input type="hidden" name="module" value="minha_clinica">
            <input type="hidden" name="action" value="financeiro_dashboard">
            <div class="input-group">
                <input type="date" name="data_inicio" class="form-control" value="<?= $dataInicio ?>">
                <input type="date" name="data_fim" class="form-control" value="<?= $dataFim ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i></button>
                </div>
            </div>
        </form>
    </div>

    <!-- Cards de Totais -->
    <div class="row">
        <!-- A Receber (Particular) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                A Receber (Particular)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($totalPrevistoParticular, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- A Faturar (Convenio) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                A Faturar (Convenio)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($totalPrevistoConvenio, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-medical-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Realizado -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Caixa Realizado (Entradas)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($totalRealizado, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendente -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendente (Aberto no Período)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($totalPendente, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico Evolução -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Evolução Financeira (6 Meses)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Recebimentos Lista -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Últimas Entradas</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($ultimosRecebimentos as $rec): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold"><?= htmlspecialchars($rec['paciente_nome']) ?></span>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($rec['convenio_nome']) ?> -
                                        <?= date('d/m', strtotime($rec['data_recebimento'])) ?></small>
                                </div>
                                <span class="badge badge-success badge-pill">
                                    + R$ <?= number_format($rec['valor_recebido'], 2, ',', '.') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($ultimosRecebimentos)): ?>
                            <li class="list-group-item text-center text-muted">Nenhum recebimento recente.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuração do Gráfico
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($dadosGrafico['labels']) ?>,
            datasets: [{
                label: "Previsto (Contas a Receber)",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: <?= json_encode($dadosGrafico['previsto']) ?>,
            }, {
                label: "Realizado (Caixa)",
                backgroundColor: "#1cc88a",
                hoverBackgroundColor: "#17a673",
                borderColor: "#1cc88a",
                data: <?= json_encode($dadosGrafico['realizado']) ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    },
                    maxBarThickness: 25,
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
                        // Include a dollar sign in the ticks
                        callback: function (value, index, values) {
                            return 'R$ ' + value.toLocaleString("pt-BR", { minimumFractionDigits: 2 });
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function (tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': R$ ' + tooltipItem.yLabel.toLocaleString("pt-BR", { minimumFractionDigits: 2 });
                    }
                }
            },
        }
    });
</script>