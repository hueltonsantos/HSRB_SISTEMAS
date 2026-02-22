<?php
// Ensure variables are set
$filtroProcedimento = isset($filtros['procedimento']) ? $filtros['procedimento'] : '';
$filtroEspecialidade = isset($filtros['especialidade_id']) ? $filtros['especialidade_id'] : '';
$filtroClinica = isset($filtros['clinica_id']) ? $filtros['clinica_id'] : '';

$canViewFinancials = hasPermission('financial_view') || (isset($_SESSION['perfil_id']) && $_SESSION['perfil_id'] == 1);
?>

<div class="container-fluid">
    <h1 class="mt-4">Tabela de Preços</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Tabela de Preços</li>
    </ol>
    
    <!-- Filtros -->
    <div class="card mb-4 shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-search mr-1"></i> Filtros</h6>
        </div>
        <div class="card-body">
            <form method="get" action="index.php">
                <input type="hidden" name="module" value="tabela_precos">
                
                <div class="form-row">
                    <div class="col-sm-6 col-md-4 mb-3">
                        <label for="procedimento">Procedimento</label>
                        <input type="text" class="form-control" id="procedimento" name="procedimento" 
                               value="<?= htmlspecialchars($filtroProcedimento) ?>">
                    </div>
                    
                    <div class="col-sm-6 col-md-3 mb-3">
                        <label for="especialidade_id">Especialidade</label>
                        <select class="form-control" id="especialidade_id" name="especialidade_id">
                            <option value="">Todas</option>
                            <?php foreach ($especialidades as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= $filtroEspecialidade == $e['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-sm-6 col-md-3 mb-3">
                        <label for="clinica_id">Clínica</label>
                        <select class="form-control" id="clinica_id" name="clinica_id">
                            <option value="">Todas</option>
                            <?php foreach ($clinicas as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $filtroClinica == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-sm-6 col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Pesquisar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabela -->
    <div class="card mb-4 shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table mr-1"></i> Preços e Procedimentos</h6>
             <?php if ($canViewFinancials): ?>
            <a href="index.php?module=tabela_precos&action=form" class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-plus"></i> Novo Preço
            </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTablePrices" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Procedimento</th>
                            <th>Especialidade</th>
                            <th>Clínica/Local</th>
                            <th>Valor (Paciente)</th>
                            <?php if ($canViewFinancials): ?>
                                <th>Custo (Repasse)</th>
                                <th>Lucro Previsto</th>
                                <th>Ações</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($precos)): ?>
                            <tr>
                                <td colspan="<?= $canViewFinancials ? 7 : 4 ?>" class="text-center">Nenhum registro encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($precos as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['procedimento']) ?></td>
                                    <td><?= htmlspecialchars($row['especialidade']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['clinica']) ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($row['cidade']) ?>/<?= htmlspecialchars($row['estado']) ?>
                                        </small>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">
                                        R$ <?= number_format($row['valor_paciente'], 2, ',', '.') ?>
                                    </td>
                                    <?php if ($canViewFinancials): ?>
                                        <td class="text-right text-danger">
                                            R$ <?= number_format($row['valor_repasse'], 2, ',', '.') ?>
                                        </td>
                                        <td class="text-right text-primary">
                                            R$ <?= number_format($row['valor_paciente'] - $row['valor_repasse'], 2, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?module=tabela_precos&action=form&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- DataTables CSS and JS -->
<link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTablePrices').DataTable({
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
        pageLength: 25,
        ordering: true
    });
});
</script>