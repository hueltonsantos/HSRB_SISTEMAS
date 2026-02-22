<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detalhes do Repasse #<?php echo $repasse['id']; ?></h1>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Informações do Repasse -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Clínica:</strong></td>
                            <td><?php echo htmlspecialchars($repasse['clinica_nome']); ?></td>
                        </tr>
                        <?php if (!empty($repasse['clinica_cnpj'])): ?>
                        <tr>
                            <td><strong>CNPJ:</strong></td>
                            <td><?php echo htmlspecialchars($repasse['clinica_cnpj']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong>Período:</strong></td>
                            <td>
                                <?php echo $caixaModel->formatDateForDisplay($repasse['periodo_inicio']); ?>
                                a <?php echo $caixaModel->formatDateForDisplay($repasse['periodo_fim']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Valor Total:</strong></td>
                            <td class="font-weight-bold">R$ <?php echo number_format($repasse['valor_total'], 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Valor Pago:</strong></td>
                            <td class="text-success">R$ <?php echo number_format($repasse['valor_pago'], 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Saldo Restante:</strong></td>
                            <td class="text-danger font-weight-bold">
                                R$ <?php echo number_format($repasse['valor_total'] - $repasse['valor_pago'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <?php
                                $statusClass = 'secondary';
                                if ($repasse['status'] === 'pendente') $statusClass = 'warning';
                                elseif ($repasse['status'] === 'parcial') $statusClass = 'info';
                                elseif ($repasse['status'] === 'pago') $statusClass = 'success';
                                ?>
                                <span class="badge badge-<?php echo $statusClass; ?>"><?php echo ucfirst($repasse['status']); ?></span>
                            </td>
                        </tr>
                        <?php if ($repasse['data_pagamento']): ?>
                        <tr>
                            <td><strong>Último Pagamento:</strong></td>
                            <td><?php echo $caixaModel->formatDateForDisplay($repasse['data_pagamento']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong>Gerado por:</strong></td>
                            <td><?php echo htmlspecialchars($repasse['usuario_nome']); ?></td>
                        </tr>
                    </table>

                    <?php if (hasPermission('repasse_manage') && $repasse['status'] !== 'pago'): ?>
                        <hr>
                        <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#pagamentoModal">
                            <i class="fas fa-money-bill-wave"></i> Registrar Pagamento
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <a href="index.php?module=caixa&action=repasses" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <!-- Itens do Repasse -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Procedimentos (<?php echo count($repasse['itens']); ?> itens)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Paciente</th>
                                    <th>Procedimento</th>
                                    <th class="text-right">Valor Proc.</th>
                                    <th class="text-right">Valor Repasse</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalProcedimentos = 0;
                                $totalRepasseItens = 0;
                                foreach ($repasse['itens'] as $item):
                                    $totalProcedimentos += (float)$item['valor_procedimento'];
                                    $totalRepasseItens += (float)$item['valor_repasse'];
                                ?>
                                    <tr>
                                        <td><?php echo $caixaModel->formatDateForDisplay($item['data_consulta']); ?></td>
                                        <td><?php echo htmlspecialchars($item['paciente_nome'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($item['procedimento_nome'] ?? '-'); ?></td>
                                        <td class="text-right">R$ <?php echo number_format($item['valor_procedimento'], 2, ',', '.'); ?></td>
                                        <td class="text-right">R$ <?php echo number_format($item['valor_repasse'], 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="3" class="text-right">TOTAIS:</td>
                                    <td class="text-right">R$ <?php echo number_format($totalProcedimentos, 2, ',', '.'); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($totalRepasseItens, 2, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Pagamento -->
<?php if (hasPermission('repasse_manage') && $repasse['status'] !== 'pago'): ?>
<div class="modal fade" id="pagamentoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php?module=caixa&action=salvar_repasse" method="post">
                <input type="hidden" name="acao" value="pagamento">
                <input type="hidden" name="repasse_id" value="<?php echo $repasse['id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pagamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Saldo restante:</strong> R$ <?php echo number_format($repasse['valor_total'] - $repasse['valor_pago'], 2, ',', '.'); ?>
                    </div>
                    <div class="form-group">
                        <label for="valor_pago">Valor do Pagamento (R$) *</label>
                        <input type="text" class="form-control money" id="valor_pago" name="valor_pago"
                            value="<?php echo number_format($repasse['valor_total'] - $repasse['valor_pago'], 2, ',', '.'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre o pagamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('.money').mask('#.##0,00', {reverse: true});
});
</script>
