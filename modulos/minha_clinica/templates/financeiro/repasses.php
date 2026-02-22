<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <button class="btn btn-success shadow-sm" onclick="window.print()">
            <i class="fas fa-print fa-sm text-white-50"></i> Imprimir
        </button>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4 d-print-none">
        <div class="card-body">
            <form method="GET" class="form-row align-items-end">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="repasses">

                <div class="col-md-4 mb-3">
                    <label>Período de Recebimento</label>
                    <div class="input-group">
                        <input type="date" name="data_inicio" class="form-control" value="<?= $dataInicio ?>">
                        <input type="date" name="data_fim" class="form-control" value="<?= $dataFim ?>">
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Profissional</label>
                    <select name="profissional_id" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($profissionais as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($profissionalId == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Repasses -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-info text-white">
            <h6 class="m-0 font-weight-bold">Detalhamento de Valores (Regime de Caixa)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th>Data Rec.</th>
                            <th>Profissional</th>
                            <th>Paciente / Guia</th>
                            <th>Convênio</th>
                            <th>Valor Recebido</th>
                            <th>Regra (%)</th>
                            <th>Valor Repasse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($relatorio)): ?>
                            <tr><td colspan="7" class="text-center">Nenhum recebimento no período.</td></tr>
                        <?php else: ?>
                            <?php 
                            $atualProf = null; 
                            $subtotalRecebido = 0;
                            $subtotalRepasse = 0;
                            ?>
                            <?php foreach ($relatorio as $r): ?>
                                <?php 
                                // Opcional: Agrupar visualmente por profissional
                                if ($atualProf !== $r['profissional_id']) {
                                    if ($atualProf !== null) {
                                        // Fechar grupo anterior (opcional)
                                    }
                                    $atualProf = $r['profissional_id'];
                                }
                                $subtotalRecebido += $r['valor_recebido'];
                                $subtotalRepasse += $r['valor_repasse'];
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['data_recebimento'])) ?></td>
                                    <td><?= htmlspecialchars($r['profissional_nome']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($r['paciente_nome']) ?>
                                        <br><small class="text-muted">Guia: <?= $r['numero_guia'] ?></small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($r['convenio_nome'] ?? 'Particular') ?>
                                        <br><small><?= htmlspecialchars($r['procedimentos_nomes']) ?></small>
                                    </td>
                                    <td class="text-right">R$ <?= number_format($r['valor_recebido'], 2, ',', '.') ?></td>
                                    <td class="text-center"><small><?= $r['regra_repasse'] ?></small></td>
                                    <td class="text-right font-weight-bold text-success">
                                        R$ <?= number_format($r['valor_repasse'], 2, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-200 font-weight-bold">
                            <td colspan="4" class="text-right">TOTAIS DO PERÍODO:</td>
                            <td class="text-right">R$ <?= number_format($subtotalRecebido ?? 0, 2, ',', '.') ?></td>
                            <td></td>
                            <td class="text-right text-success">R$ <?= number_format($totalRepasseGeral, 2, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Nota:</strong> O cálculo de repasse é baseado no <em>Valor Efetivamente Recebido</em> (Caixa Realizado).
                        Valores previstos (a receber) não entram neste relatório até que a guia seja baixada.
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <?php if ($profissionalId && !empty($relatorio)): ?>
                        <button type="button" class="btn btn-lg btn-primary shadow" id="btnFecharRepasse">
                            <i class="fas fa-check-double"></i> Fechar Repasse
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnFecharRepasse').click(function() {
        if (!confirm('Deseja fechar o repasse para este profissional no período selecionado? Isso irá gerar um registro de Contas a Pagar e bloqueará os itens para novos cálculos.')) {
            return;
        }

        var profissionalId = "<?= $profissionalId ?>";
        var dataInicio = "<?= $dataInicio ?>";
        var dataFim = "<?= $dataFim ?>";

        $.post('index.php?module=minha_clinica&action=fechar_repasse', {
            profissional_id: profissionalId,
            data_inicio: dataInicio,
            data_fim: dataFim
        }, function(response) {
            if (response.success) {
                alert('Repasse fechado com sucesso! ID: #' + response.repasse_id);
                location.reload();
            } else {
                alert('Erro ao fechar: ' + response.message);
            }
        }, 'json');
    });
});
</script>
