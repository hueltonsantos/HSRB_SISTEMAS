<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice-dollar mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=convenios" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Voltar para Convênios
        </a>
    </div>

    <!-- Seleção de Convênio -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="form-inline justify-content-center">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="tabela_precos">
                
                <label class="mr-3 font-weight-bold">Selecione o Convênio:</label>
                <select name="convenio_id" class="form-control mr-3" onchange="this.form.submit()">
                    <option value="">-- Selecione --</option>
                    <?php foreach ($convenios as $conv): ?>
                        <option value="<?= $conv['id'] ?>" <?= ($convenioId == $conv['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($conv['nome_fantasia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if ($convenioId): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">Tabela de Preços</h6>
            <small class="d-block mt-1">Defina os valores que este convênio paga por procedimento</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTablePrecos" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Especialidade</th>
                            <th>Procedimento</th>
                            <th>Valor Base (Part.)</th>
                            <th width="150" class="bg-warning text-dark">Valor Convênio</th>
                            <th width="150" class="bg-warning text-dark">Código (TUSS)</th>
                            <th width="120" title="Deixe em branco para usar o padrão do profissional">Repasse % <i class="fas fa-info-circle"></i></th>
                            <th width="80">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($procedimentos as $proc): ?>
                            <?php 
                                $preco = $precosDefinidos[$proc['id']] ?? null;
                                $valorConvenio = $preco ? $preco['valor'] : $proc['valor'];
                                $codigo = $preco ? $preco['codigo_tuss'] : $proc['codigo_padrao'];
                                $repasse = $preco ? $preco['repasse_percentual'] : '';
                            ?>
                            <tr data-proc-id="<?= $proc['id'] ?>">
                                <td><small><?= htmlspecialchars($proc['especialidade_nome']) ?></small></td>
                                <td><strong><?= htmlspecialchars($proc['procedimento']) ?></strong></td>
                                <td class="text-muted">R$ <?= number_format($proc['valor'], 2, ',', '.') ?></td>
                                
                                <!-- Campos Editáveis -->
                                <td class="p-1">
                                    <input type="number" step="0.01" class="form-control form-control-sm valor-input" 
                                           value="<?= $valorConvenio ?>" placeholder="0.00">
                                </td>
                                <td class="p-1">
                                    <input type="text" class="form-control form-control-sm codigo-input" 
                                           value="<?= htmlspecialchars($codigo ?? '') ?>" placeholder="Código">
                                </td>
                                <td class="p-1">
                                    <input type="number" step="0.01" class="form-control form-control-sm repasse-input" 
                                           value="<?= $repasse ?>" placeholder="Padrão">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm btn-save" title="Salvar">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php elseif (isset($_GET['convenio_id'])): ?>
        <div class="alert alert-warning text-center">Selecione um convênio válido.</div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Inicializa DataTable simple
    if ($('#dataTablePrecos tbody tr').length > 0) {
        var table = $('#dataTablePrecos').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json' },
            paging: false, // Mostrar tudo numa pagina para editar rápido
            order: [[0, 'asc'], [1, 'asc']]
        });
    }

    // Ação de Salvar AJAX
    $('.btn-save').click(function() {
        var tr = $(this).closest('tr');
        var btn = $(this);
        var originalIcon = btn.html();
        
        var data = {
            convenio_id: <?= $convenioId ? $convenioId : 0 ?>,
            procedimento_id: tr.data('proc-id'),
            valor: tr.find('.valor-input').val(),
            codigo_tuss: tr.find('.codigo-input').val(),
            repasse_percentual: tr.find('.repasse-input').val()
        };

        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.post('index.php?module=minha_clinica&action=salvar_preco_ajax', data, function(response) {
            if (response.success) {
                btn.html('<i class="fas fa-check"></i>').removeClass('btn-success').addClass('btn-primary');
                setTimeout(function() {
                    btn.html('<i class="fas fa-save"></i>').removeClass('btn-primary').addClass('btn-success').prop('disabled', false);
                }, 1500);
            } else {
                alert('Erro ao salvar: ' + response.message);
                btn.html(originalIcon).prop('disabled', false);
            }
        }, 'json').fail(function() {
            alert('Erro de comunicação com o servidor.');
            btn.html(originalIcon).prop('disabled', false);
        });
    });

    // Salvar ao pressionar Enter nos inputs
    $('input').keypress(function(e) {
        if (e.which == 13) {
            $(this).closest('tr').find('.btn-save').click();
        }
    });
});
</script>
