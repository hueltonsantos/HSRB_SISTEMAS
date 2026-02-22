<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">Vínculos e Repasses</h6>
        </div>
        <div class="card-body">
            
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['mensagem']['texto'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['mensagem']); ?>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTableConfigProf">
                    <thead class="thead-light">
                        <tr>
                            <th>Profissional</th>
                            <th>Registro Conselho</th>
                            <th>Usuário de Sistema (Login)</th>
                            <th>Repasse Padrão (%)</th>
                            <th width="100">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($profissionais as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nome']) ?></td>
                                <td><?= htmlspecialchars($p['registro_profissional']) ?></td>
                                <td>
                                    <?php if ($p['usuario_nome']): ?>
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-user-check"></i> <?= htmlspecialchars($p['usuario_nome']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">Sem vínculo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center font-weight-bold">
                                    <?= number_format($p['repasse_padrao_percentual'] ?? 0, 2, ',', '.') ?>%
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary btn-editar" 
                                            data-id="<?= $p['id'] ?>"
                                            data-nome="<?= htmlspecialchars($p['nome']) ?>"
                                            data-usuario="<?= $p['usuario_sistema_id'] ?>"
                                            data-repasse="<?= number_format($p['repasse_padrao_percentual'] ?? 0, 2, ',', '.') ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Configuração -->
<div class="modal fade" id="modalConfig" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Profissional</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="index.php?module=minha_clinica&action=config_profissionais">
                <div class="modal-body">
                    <input type="hidden" name="profissional_id" id="conf_profissional_id">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Profissional</label>
                        <input type="text" class="form-control" id="conf_nome" readonly>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Usuário de Login (Vínculo)</label>
                        <select name="usuario_id" id="conf_usuario_id" class="form-control select2-modal">
                            <option value="">-- Sem vínculo --</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id'] ?>">
                                    <?= htmlspecialchars($u['nome']) ?> (<?= $u['email'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            Vincule este profissional a um usuário para que ele possa acessar o "Painel do Profissional".
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Repasse Padrão (%)</label>
                        <div class="input-group">
                            <input type="text" class="form-control money" name="repasse_padrao" id="conf_repasse" required>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Percentual base aplicado sobre o valor recebido, salvo exceções na Tabela de Preços.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Configuração</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTableConfigProf').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json' }
    });

    $('.btn-editar').click(function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');
        var usuario = $(this).data('usuario');
        var repasse = $(this).data('repasse');

        $('#conf_profissional_id').val(id);
        $('#conf_nome').val(nome);
        $('#conf_usuario_id').val(usuario);
        $('#conf_repasse').val(repasse);

        $('#modalConfig').modal('show');
    });

    // Mascara simples para % na falta do plugin
    $('#conf_repasse').on('input', function() {
        this.value = this.value.replace(/[^0-9,]/g, '');
    });
});
</script>
