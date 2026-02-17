<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Relatórios Gerenciais</h1>
    
    <div class="row">

    <!-- Dashboard Charts -->
    <div class="row mb-4 w-100">
        <!-- Chart Profissional (Volume por Clínica) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Volume por Clínica (Profissional) - Últimos 30 dias</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="myBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Qualidade (Status) -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Qualidade (Status)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Configuração do Gráfico de Barras - Volume por Clínica
    var ctxBar = document.getElementById("myBarChart");
    var myBarChart = new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: [<?php 
            $labels = [];
            foreach($dashboardStats['clinicas'] as $c) $labels[] = $c['nome'];
            echo "'" . implode("', '", $labels) . "'";
        ?>],
        datasets: [{
          label: "Agendamentos",
          backgroundColor: "#4e73df",
          hoverBackgroundColor: "#2e59d9",
          borderColor: "#4e73df",
          data: [<?php 
            $data = [];
            foreach($dashboardStats['clinicas'] as $c) $data[] = $c['total'];
            echo implode(", ", $data);
          ?>],
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: { left: 10, right: 25, top: 25, bottom: 0 }
        },
        scales: {
          x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 6 } },
          y: { ticks: { maxTicksLimit: 5, padding: 10, beginAtZero: true }, grid: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } },
        },
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: "rgb(255,255,255)", bodyColor: "#858796", titleColor: '#6e707e', borderColor: '#dddfeb', borderWidth: 1 }
        }
      }
    });

    // Configuração do Gráfico de Pizza - Status (Qualidade)
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        labels: [<?php 
            $labelsClean = [];
            foreach($dashboardStats['status'] as $s) $labelsClean[] = ucfirst($s['status_agendamento']);
            echo "'" . implode("', '", $labelsClean) . "'";
        ?>],
        datasets: [{
          data: [<?php 
            $data = [];
            foreach($dashboardStats['status'] as $s) $data[] = $s['total'];
            echo implode(", ", $data);
          ?>],
          backgroundColor: ['#4e73df', '#1cc88a', '#e74a3b', '#f6c23e', '#36b9cc'],
          hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { backgroundColor: "rgb(255,255,255)", bodyColor: "#858796", borderColor: '#dddfeb', borderWidth: 1 }
        },
        cutout: '80%',
      },
    });
    </script>
        <!-- Card Financeiro -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Financeiro Logic</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Relatório Financeiro</div>
                            <p class="mt-2 text-muted small">Receitas, repasses e lucro líquido.</p>
                            <a href="index.php?module=relatorios&action=financeiro" class="btn btn-sm btn-success mt-2">Acessar</a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Operacional -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Operacional</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Relatório Operacional</div>
                            <p class="mt-2 text-muted small">Volume de guias por clínica e especialidade.</p>
                            <a href="index.php?module=relatorios&action=operacional" class="btn btn-sm btn-primary mt-2">Acessar</a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
