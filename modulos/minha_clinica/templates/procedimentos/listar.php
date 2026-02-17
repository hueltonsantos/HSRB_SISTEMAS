<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-procedures mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=novo_procedimento" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm"></i> Novo Procedimento
        </a>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] ?> alert-dismissible fade show">
            <?= $_SESSION['mensagem']['texto'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="form-inline">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="procedimentos">
                <div class="form-group mr-3 mb-2">
                    <label for="especialidade_id" class="mr-2">Especialidade:</label>
                    <select class="form-control form-control-sm" name="especialidade_id" id="especialidade_id">
                        <option value="">Todas</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?= $esp['id'] ?>"
                                <?= ($especialidadeId == $esp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm mb-2 mr-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="index.php?module=minha_clinica&action=procedimentos" class="btn btn-secondary btn-sm mb-2">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Procedimentos</h6>
        </div>
        <div class="card-body">
            <?php if (empty($procedimentos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-procedures fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">Nenhum procedimento cadastrado</p>
                    <a href="index.php?module=minha_clinica&action=novo_procedimento" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Cadastrar Procedimento
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Procedimento</th>
                                <th>Especialidade</th>
                                <th width="120">Valor</th>
                                <th width="100">Duracao</th>
                                <th width="80">Status</th>
                                <th width="150">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($procedimentos as $proc): ?>
                                <tr>
                                    <td><?= $proc['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($proc['procedimento']) ?></strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= htmlspecialchars($proc['especialidade_nome'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">
                                        R$ <?= number_format($proc['valor'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $proc['duracao_minutos'] ?> min
                                    </td>
                                    <td class="text-center">
                                        <?php if ($proc['status']): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?module=minha_clinica&action=editar_procedimento&id=<?= $proc['id'] ?>"
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?module=minha_clinica&action=deletar_procedimento&id=<?= $proc['id'] ?>"
                                           class="btn btn-danger btn-sm"
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir este procedimento?')">
                                            <i class="fas fa-trash"></i>
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
</div>

<script>
$(document).ready(function() {
    if ($('#dataTable tbody tr').length > 0 && !$('#dataTable').hasClass('dataTable')) {
        $('#dataTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
            },
            order: [[2, 'asc'], [1, 'asc']],
            pageLength: 25
        });
    }
});
</script>
