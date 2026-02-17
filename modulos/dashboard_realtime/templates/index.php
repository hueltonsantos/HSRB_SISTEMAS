<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line text-primary"></i> Painel em Tempo Real
        </h1>
        <div>
            <button class="btn btn-primary btn-sm" id="btnRefresh" title="Atualizar dados">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
            <span class="badge badge-secondary ml-2" id="lastUpdate">
                <i class="fas fa-clock"></i> Última atualização: --:--
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <form id="formFiltros" class="row">
                <div class="col-md-2">
                    <label for="data_inicio">Criado de</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-2">
                    <label for="data_fim">Até</label>
                    <input type="date" name="data_fim" id="data_fim" class="form-control"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-2">
                    <label for="clinica_id">Clínica</label>
                    <select name="clinica_id" id="clinica_id" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($clinicas as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="especialidade_id">Especialidade</label>
                    <select name="especialidade_id" id="especialidade_id" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($especialidades as $e): ?>
                            <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="usuario_id">Usuário</label>
                    <select name="usuario_id" id="usuario_id" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Totais -->
    <div class="row" id="cards-totais">
        <!-- Card Total Agendamentos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Agendamentos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-agendamentos">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Faturamento -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Faturamento Bruto
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="faturamento">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Repasse -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Repasse
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="repasse">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Lucro -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Lucro Líquido
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lucro">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Linha 1 -->
    <div class="row">
        <!-- Gráfico por Usuário -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user"></i> Agendamentos por Usuário
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="chartUsuarios"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico por Especialidade -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-stethoscope"></i> Por Especialidade
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="chartEspecialidades"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Linha 2 -->
    <div class="row">
        <!-- Gráfico por Procedimento -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-procedures"></i> Top Procedimentos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="chartProcedimentos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico Forma de Pagamento -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card"></i> Formas de Pagamento
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="chartPagamentos"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela Detalhada -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table"></i> Detalhamento dos Agendamentos
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabelaDetalhes" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Clínica</th>
                            <th>Especialidade</th>
                            <th>Procedimentos</th>
                            <th>Valor</th>
                            <th>Pagamento</th>
                            <th>Criado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Carregando...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts do Dashboard -->
<script>
// Variáveis globais para os gráficos
let chartUsuarios, chartEspecialidades, chartProcedimentos, chartPagamentos;
let autoRefreshInterval = null;
let dataTable = null;
const REFRESH_INTERVAL = 30000; // 30 segundos

// Paleta de cores
const paletaCores = [
    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
    '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf',
    '#fd7e14', '#6f42c1', '#20c997', '#6610f2', '#e83e8c'
];

// Inicialização quando o DOM estiver pronto
$(document).ready(function() {
    // Inicializar gráficos vazios
    initCharts();

    // Carregar dados iniciais
    carregarDados();

    // Iniciar auto-refresh
    startAutoRefresh();

    // Evento de filtro
    $('#formFiltros').on('submit', function(e) {
        e.preventDefault();
        carregarDados();
    });

    // Botão refresh manual
    $('#btnRefresh').on('click', function() {
        carregarDados();
    });
});

/**
 * Inicializa os gráficos vazios
 */
function initCharts() {
    // Gráfico por Usuário (Barra horizontal)
    chartUsuarios = new Chart(document.getElementById('chartUsuarios'), {
        type: 'horizontalBar',
        data: {
            labels: [],
            datasets: [{
                label: 'Agendamentos',
                data: [],
                backgroundColor: paletaCores
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                xAxes: [{
                    ticks: { beginAtZero: true }
                }]
            }
        }
    });

    // Gráfico por Especialidade (Doughnut)
    chartEspecialidades = new Chart(document.getElementById('chartEspecialidades'), {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: paletaCores
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom' },
            cutoutPercentage: 60
        }
    });

    // Gráfico por Procedimento (Barra vertical)
    chartProcedimentos = new Chart(document.getElementById('chartProcedimentos'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Quantidade',
                data: [],
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: { beginAtZero: true }
                }],
                xAxes: [{
                    ticks: {
                        callback: function(value) {
                            return value.length > 15 ? value.substr(0, 15) + '...' : value;
                        }
                    }
                }]
            }
        }
    });

    // Gráfico Forma de Pagamento (Pizza)
    chartPagamentos = new Chart(document.getElementById('chartPagamentos'), {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: paletaCores
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'right' }
        }
    });
}

/**
 * Carrega todos os dados via AJAX
 */
function carregarDados() {
    const formData = $('#formFiltros').serialize();

    $.ajax({
        url: 'index.php?module=dashboard_realtime&action=api&api_action=all',
        method: 'GET',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#btnRefresh').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Atualizando...');
        },
        success: function(response) {
            if (response.success) {
                atualizarCards(response.totais);
                atualizarGraficoUsuarios(response.por_usuario);
                atualizarGraficoEspecialidades(response.por_especialidade);
                atualizarGraficoProcedimentos(response.por_procedimento);
                atualizarGraficoPagamentos(response.por_pagamento);
                atualizarTabela(response.lista);

                // Atualizar timestamp
                const now = new Date();
                $('#lastUpdate').html('<i class="fas fa-clock"></i> Atualizado: ' + now.toLocaleTimeString('pt-BR'));
            } else {
                console.error('Erro:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX:', error);
        },
        complete: function() {
            $('#btnRefresh').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Atualizar');
        }
    });
}

/**
 * Atualiza os cards de totais
 */
function atualizarCards(totais) {
    $('#total-agendamentos').text(totais.total_agendamentos || 0);
    $('#faturamento').text('R$ ' + formatMoney(totais.faturamento_bruto || 0));
    $('#repasse').text('R$ ' + formatMoney(totais.total_repasse || 0));
    $('#lucro').text('R$ ' + formatMoney(totais.lucro_liquido || 0));
}

/**
 * Atualiza gráfico de usuários
 */
function atualizarGraficoUsuarios(dados) {
    if (!dados || dados.length === 0) {
        chartUsuarios.data.labels = ['Sem dados'];
        chartUsuarios.data.datasets[0].data = [0];
    } else {
        chartUsuarios.data.labels = dados.map(d => d.usuario || 'Desconhecido');
        chartUsuarios.data.datasets[0].data = dados.map(d => parseInt(d.total) || 0);
    }
    chartUsuarios.update();
}

/**
 * Atualiza gráfico de especialidades
 */
function atualizarGraficoEspecialidades(dados) {
    if (!dados || dados.length === 0) {
        chartEspecialidades.data.labels = ['Sem dados'];
        chartEspecialidades.data.datasets[0].data = [0];
    } else {
        chartEspecialidades.data.labels = dados.map(d => d.especialidade || 'Desconhecida');
        chartEspecialidades.data.datasets[0].data = dados.map(d => parseInt(d.total) || 0);
    }
    chartEspecialidades.update();
}

/**
 * Atualiza gráfico de procedimentos
 */
function atualizarGraficoProcedimentos(dados) {
    if (!dados || dados.length === 0) {
        chartProcedimentos.data.labels = ['Sem dados'];
        chartProcedimentos.data.datasets[0].data = [0];
    } else {
        chartProcedimentos.data.labels = dados.map(d => {
            const nome = d.procedimento || 'Desconhecido';
            return nome.length > 20 ? nome.substring(0, 20) + '...' : nome;
        });
        chartProcedimentos.data.datasets[0].data = dados.map(d => parseInt(d.quantidade) || 0);
    }
    chartProcedimentos.update();
}

/**
 * Atualiza gráfico de formas de pagamento
 */
function atualizarGraficoPagamentos(dados) {
    if (!dados || dados.length === 0) {
        chartPagamentos.data.labels = ['Sem dados'];
        chartPagamentos.data.datasets[0].data = [0];
    } else {
        chartPagamentos.data.labels = dados.map(d => d.forma_pagamento || 'Não informado');
        chartPagamentos.data.datasets[0].data = dados.map(d => parseInt(d.total) || 0);
    }
    chartPagamentos.update();
}

/**
 * Atualiza tabela de detalhamento
 */
function atualizarTabela(dados) {
    // Destruir DataTable existente se houver
    if (dataTable) {
        dataTable.destroy();
        dataTable = null;
    }

    // Limpar tbody
    const tbody = $('#tabelaDetalhes tbody');
    tbody.empty();

    if (!dados || dados.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">Nenhum agendamento encontrado no período.</td></tr>');
        return; // Não inicializa DataTable se não há dados
    }

    // Adicionar dados
    dados.forEach(d => {
        tbody.append(`
            <tr>
                <td>${formatDate(d.data_consulta)}</td>
                <td>${formatTime(d.hora_consulta)}</td>
                <td>${escapeHtml(d.paciente_nome || '-')}</td>
                <td>${escapeHtml(d.clinica_nome || '-')}</td>
                <td>${escapeHtml(d.especialidade_nome || '-')}</td>
                <td>${escapeHtml(d.procedimentos || '-')}</td>
                <td class="text-right">R$ ${formatMoney(d.valor_total || 0)}</td>
                <td>${escapeHtml(d.forma_pagamento || 'Não informado')}</td>
                <td>${escapeHtml(d.usuario_criador || '-')}</td>
            </tr>
        `);
    });

    // Reinicializar DataTable com botões de exportação
    dataTable = $('#tabelaDetalhes').DataTable({
        language: {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                title: 'Relatorio_Dashboard_' + new Date().toISOString().slice(0, 10)
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible'
                },
                title: 'Relatório Dashboard - ' + new Date().toLocaleDateString('pt-BR')
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
}

/**
 * Inicia o auto-refresh
 */
function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    autoRefreshInterval = setInterval(carregarDados, REFRESH_INTERVAL);
}

/**
 * Formata valor monetário
 */
function formatMoney(value) {
    return parseFloat(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Formata data
 */
function formatDate(date) {
    if (!date) return '-';
    const d = new Date(date + 'T00:00:00');
    return d.toLocaleDateString('pt-BR');
}

/**
 * Formata hora
 */
function formatTime(time) {
    if (!time) return '-';
    return time.substring(0, 5);
}

/**
 * Escapa HTML para evitar XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<!-- CSS adicional para DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
