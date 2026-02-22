<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Relatório Operacional</h1>
        <a href="index.php?module=relatorios" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 shadow border-bottom-primary">
        <div class="card-body">
            <form method="get" action="index.php" class="form-inline">
                <input type="hidden" name="module" value="relatorios">
                <input type="hidden" name="action" value="operacional">
                
                <label class="mr-2">Período:</label>
                <input type="date" name="inicio" class="form-control mr-2" value="<?= $inicio ?>" required>
                <span>até</span>
                <input type="date" name="fim" class="form-control mx-2" value="<?= $fim ?>" required>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <!-- Top Clínica -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Top Clínica (Volume)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($stats['top_clinica']['nome']) ?></div>
                            <div class="text-xs text-muted font-weight-bold mt-1"><?= $stats['top_clinica']['total'] ?> agendamentos</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hospital fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Especialidade -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Top Especialidade</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($stats['top_especialidade']['nome']) ?></div>
                            <div class="text-xs text-muted font-weight-bold mt-1"><?= $stats['top_especialidade']['total'] ?> agendamentos</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stethoscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Geral -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total de Agendamentos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_geral'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalhamento Operacional</h6>
        </div>
        <div class="card-body">
            <?php if (empty($dados)): ?>
                <div class="alert alert-info">Nenhum dado encontrado para o período selecionado.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableOperacional" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Especialidade</th>
                                <th>Procedimento</th>
                                <th>Clínica</th>
                                <th class="text-center">Qtd.</th>
                                <th class="text-right">Valor Total Movimentado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['especialidade']) ?></td>
                                <td><?= htmlspecialchars($row['procedimento']) ?></td>
                                <td><?= htmlspecialchars($row['clinica']) ?></td>
                                <td class="text-center font-weight-bold"><?= $row['qtd'] ?></td>
                                <td class="text-right text-success">R$ <?= number_format($row['valor_total'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTableOperacional').DataTable({
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
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm'
            }
        ],
        pageLength: 25,
        ordering: true,
        order: [[3, "desc"]] // Ordenar por Qtd
    });
});
</script>
