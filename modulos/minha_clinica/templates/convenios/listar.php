<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-medkit mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=novo_convenio" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm"></i> Novo Convênio
        </a>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] ?> alert-dismissible fade show">
            <?= $_SESSION['mensagem']['texto'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Convênios</h6>
        </div>
        <div class="card-body">
            <?php if (empty($convenios)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-hospital-alt fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">Nenhum convênio cadastrado</p>
                    <a href="index.php?module=minha_clinica&action=novo_convenio" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Cadastrar Convênio
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nome Fantasia</th>
                                <th>Razão Social</th>
                                <th>CNPJ</th>
                                <th>Dias Retorno</th>
                                <th>Prazo Pgto</th>
                                <th width="80">Status</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($convenios as $conv): ?>
                                <tr>
                                    <td><?= $conv['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($conv['nome_fantasia']) ?></strong></td>
                                    <td><?= htmlspecialchars($conv['razao_social'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($conv['cnpj'] ?? '-') ?></td>
                                    <td><?= $conv['dias_retorno'] ?> dias</td>
                                    <td><?= $conv['prazo_recebimento_dias'] ?> dias</td>
                                    <td class="text-center">
                                        <?php if ($conv['ativo']): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?module=minha_clinica&action=editar_convenio&id=<?= $conv['id'] ?>"
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?module=minha_clinica&action=tabela_precos&convenio_id=<?= $conv['id'] ?>"
                                           class="btn btn-info btn-sm" title="Tabela de Preços">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                        <a href="index.php?module=minha_clinica&action=deletar_convenio&id=<?= $conv['id'] ?>"
                                           class="btn btn-danger btn-sm"
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja desativar este convênio?')">
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
            order: [[1, 'asc']],
            pageLength: 25
        });
    }
});
</script>
