<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Caixa</h1>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <!-- Cards Resumo -->
    <div class="row mb-4">
        <!-- Status do Caixa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-<?php echo $caixaAberto ? 'success' : 'danger'; ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?php echo $caixaAberto ? 'success' : 'danger'; ?> text-uppercase mb-1">Status do Caixa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $caixaAberto ? 'ABERTO' : 'FECHADO'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Entradas Hoje -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Entradas (Hoje)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo number_format($resumoDia['total_entradas'], 2, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Saídas Hoje -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Saídas (Hoje)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo number_format($resumoDia['total_saidas'], 2, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo do Dia -->
        <div class="col-xl-3 col-md-6 mb-4">
            <?php $saldoDia = $resumoDia['total_entradas'] - $resumoDia['total_saidas']; ?>
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Saldo (Hoje)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?php echo number_format($saldoDia, 2, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações do Caixa -->
    <?php if (hasPermission('caixa_manage')): ?>
    <div class="row mb-4">
        <div class="col-12">
            <?php if (!$caixaAberto): ?>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#abrirCaixaModal">
                    <i class="fas fa-lock-open"></i> Abrir Caixa
                </button>
            <?php else: ?>
                <a href="index.php?module=caixa&action=novo_lancamento" class="btn btn-primary mr-2">
                    <i class="fas fa-plus"></i> Novo Lançamento
                </a>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#fecharCaixaModal">
                    <i class="fas fa-lock"></i> Fechar Caixa
                </button>
                <span class="ml-3 text-muted">
                    Caixa aberto em <?php echo date('d/m/Y H:i', strtotime($caixaAberto['data_abertura'])); ?>
                    | Saldo inicial: R$ <?php echo number_format($caixaAberto['saldo_inicial'], 2, ',', '.'); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Agendamentos do Dia -->
    <?php if ($caixaAberto && !empty($agendamentosDoDia)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-check"></i> Agendamentos do Dia (<?php echo date('d/m/Y'); ?>)
            </h6>
            <span class="badge badge-primary"><?php echo count($agendamentosDoDia); ?> agendamento(s)</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Clínica</th>
                            <th>Especialidade</th>
                            <th>Procedimentos</th>
                            <th>Valor</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Caixa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentosDoDia as $ag): ?>
                            <tr class="<?php echo $ag['ja_lancado'] ? 'table-success' : ''; ?>">
                                <td><?php echo substr($ag['hora_consulta'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($ag['paciente_nome']); ?></td>
                                <td><?php echo htmlspecialchars($ag['clinica_nome'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($ag['especialidade_nome'] ?? '-'); ?></td>
                                <td><small><?php echo htmlspecialchars($ag['procedimentos'] ?? '-'); ?></small></td>
                                <td class="text-right">
                                    <?php echo $ag['valor_total'] ? 'R$ ' . number_format($ag['valor_total'], 2, ',', '.') : '-'; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($ag['forma_pagamento'] ?? '-'); ?>
                                </td>
                                <td>
                                    <?php
                                    $stClass = 'secondary';
                                    if ($ag['status_agendamento'] === 'agendado') $stClass = 'primary';
                                    elseif ($ag['status_agendamento'] === 'confirmado') $stClass = 'info';
                                    elseif ($ag['status_agendamento'] === 'realizado') $stClass = 'success';
                                    elseif ($ag['status_agendamento'] === 'cancelado') $stClass = 'danger';
                                    ?>
                                    <span class="badge badge-<?php echo $stClass; ?>">
                                        <?php echo ucfirst($ag['status_agendamento']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($ag['ja_lancado']): ?>
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Lançado</span>
                                    <?php elseif ($ag['status_agendamento'] === 'cancelado'): ?>
                                        <span class="text-muted">-</span>
                                    <?php elseif (hasPermission('caixa_manage')): ?>
                                        <button type="button" class="btn btn-success btn-sm" title="Lançar no Caixa"
                                            data-toggle="modal" data-target="#lancarModal<?php echo $ag['id']; ?>">
                                            <i class="fas fa-cash-register"></i> Lançar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php if (!$ag['ja_lancado'] && $ag['status_agendamento'] !== 'cancelado' && hasPermission('caixa_manage')): ?>
                            <!-- Modal Lançar Agendamento -->
                            <div class="modal fade" id="lancarModal<?php echo $ag['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="index.php?module=caixa&action=lancar_agendamento" method="post">
                                            <input type="hidden" name="agendamento_id" value="<?php echo $ag['id']; ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Lançar no Caixa</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Paciente:</strong> <?php echo htmlspecialchars($ag['paciente_nome']); ?></p>
                                                <p><strong>Procedimentos:</strong> <?php echo htmlspecialchars($ag['procedimentos'] ?? '-'); ?></p>
                                                <p><strong>Valor:</strong> R$ <?php echo number_format($ag['valor_total'] ?? 0, 2, ',', '.'); ?></p>
                                                <?php if (!$ag['valor_total'] || $ag['valor_total'] <= 0): ?>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Este agendamento não possui valor definido. Verifique os procedimentos.
                                                    </div>
                                                <?php endif; ?>
                                                <div class="form-group">
                                                    <label for="forma_pagamento_<?php echo $ag['id']; ?>">Forma de Pagamento</label>
                                                    <select class="form-control" name="forma_pagamento" id="forma_pagamento_<?php echo $ag['id']; ?>" required>
                                                        <?php
                                                        $fpSelecionada = '';
                                                        $mapFp = [
                                                            'Dinheiro' => 'dinheiro',
                                                            'PIX' => 'pix',
                                                            'Cartão de Crédito' => 'cartao_credito',
                                                            'Cartão de Débito' => 'cartao_debito',
                                                            'Plano de Saúde' => 'convenio',
                                                        ];
                                                        if ($ag['forma_pagamento'] && isset($mapFp[$ag['forma_pagamento']])) {
                                                            $fpSelecionada = $mapFp[$ag['forma_pagamento']];
                                                        }
                                                        ?>
                                                        <?php foreach ($formasPagamento as $key => $label): ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $fpSelecionada === $key ? 'selected' : ''; ?>>
                                                                <?php echo $label; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success" <?php echo (!$ag['valor_total'] || $ag['valor_total'] <= 0) ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-check"></i> Confirmar Lançamento
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lançamentos</h6>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <form action="index.php" method="get" class="form">
                    <input type="hidden" name="module" value="caixa">
                    <input type="hidden" name="action" value="listar">

                    <div class="row mb-3">
                        <div class="col-sm-6 col-md-3">
                            <label for="data_inicio">Data Início</label>
                            <input type="text" class="form-control datepicker" id="data_inicio" name="data_inicio"
                                value="<?php echo isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : ''; ?>"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="data_fim">Data Fim</label>
                            <input type="text" class="form-control datepicker" id="data_fim" name="data_fim"
                                value="<?php echo isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : ''; ?>"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo">
                                <option value="">Todos</option>
                                <option value="entrada" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                                <option value="saida" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'saida') ? 'selected' : ''; ?>>Saída</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="forma_pagamento">Forma Pagamento</label>
                            <select class="form-control" id="forma_pagamento" name="forma_pagamento">
                                <option value="">Todas</option>
                                <?php foreach ($formasPagamento as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($_GET['forma_pagamento']) && $_GET['forma_pagamento'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mb-2 mr-2">Filtrar</button>
                            <a href="index.php?module=caixa&action=listar" class="btn btn-secondary mb-2">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabela de Lançamentos -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Forma Pgto.</th>
                            <th>Paciente</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lancamentos)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum lançamento encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lancamentos as $lancamento): ?>
                                <tr>
                                    <td><?php echo $lancamento['id']; ?></td>
                                    <td><?php echo $caixaModel->formatDateForDisplay($lancamento['data']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $lancamento['tipo'] == 'entrada' ? 'success' : 'danger'; ?>">
                                            <?php echo $lancamento['tipo'] == 'entrada' ? 'Entrada' : 'Saída'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($lancamento['descricao']); ?></td>
                                    <td><?php echo $formasPagamento[$lancamento['forma_pagamento']] ?? $lancamento['forma_pagamento']; ?></td>
                                    <td><?php echo htmlspecialchars($lancamento['paciente_nome'] ?? '-'); ?></td>
                                    <td class="text-right font-weight-bold text-<?php echo $lancamento['tipo'] == 'entrada' ? 'success' : 'danger'; ?>">
                                        <?php echo $lancamento['tipo'] == 'entrada' ? '+' : '-'; ?>
                                        R$ <?php echo number_format($lancamento['valor'], 2, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?module=caixa&action=recibo&id=<?php echo $lancamento['id']; ?>"
                                                class="btn btn-info btn-sm" title="Recibo">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            <?php if (hasPermission('caixa_manage')): ?>
                                                <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                    data-toggle="modal" data-target="#deleteModal<?php echo $lancamento['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Modal de Exclusão -->
                                        <div class="modal fade" id="deleteModal<?php echo $lancamento['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Exclusão</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Deseja realmente excluir o lançamento <strong>#<?php echo $lancamento['id']; ?></strong>?</p>
                                                        <p><strong><?php echo htmlspecialchars($lancamento['descricao']); ?></strong> - R$ <?php echo number_format($lancamento['valor'], 2, ',', '.'); ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <form action="index.php?module=caixa&action=deletar_lancamento" method="post" style="display:inline">
                                                            <input type="hidden" name="id" value="<?php echo $lancamento['id']; ?>">
                                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Navegação de páginas">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=caixa&action=listar&page=<?php echo $page - 1; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . urlencode($_GET['data_inicio']) : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . urlencode($_GET['data_fim']) : ''; ?><?php echo isset($_GET['tipo']) ? '&tipo=' . $_GET['tipo'] : ''; ?><?php echo isset($_GET['forma_pagamento']) ? '&forma_pagamento=' . $_GET['forma_pagamento'] : ''; ?>">
                                        Anterior
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                $startPage = max(1, $endPage - 4);
                            }
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?module=caixa&action=listar&page=<?php echo $i; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . urlencode($_GET['data_inicio']) : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . urlencode($_GET['data_fim']) : ''; ?><?php echo isset($_GET['tipo']) ? '&tipo=' . $_GET['tipo'] : ''; ?><?php echo isset($_GET['forma_pagamento']) ? '&forma_pagamento=' . $_GET['forma_pagamento'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=caixa&action=listar&page=<?php echo $page + 1; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . urlencode($_GET['data_inicio']) : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . urlencode($_GET['data_fim']) : ''; ?><?php echo isset($_GET['tipo']) ? '&tipo=' . $_GET['tipo'] : ''; ?><?php echo isset($_GET['forma_pagamento']) ? '&forma_pagamento=' . $_GET['forma_pagamento'] : ''; ?>">
                                        Próximo
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

            <div class="mt-3 text-muted">
                <small>Exibindo <?php echo count($lancamentos); ?> de <?php echo $totalLancamentos; ?> lançamentos.</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal Abrir Caixa -->
<div class="modal fade" id="abrirCaixaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php?module=caixa&action=abrir_caixa" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Abrir Caixa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="saldo_inicial">Saldo Inicial (R$)</label>
                        <input type="text" class="form-control money" id="saldo_inicial" name="saldo_inicial" value="0,00" placeholder="0,00">
                        <small class="form-text text-muted">Informe o valor em dinheiro disponível no caixa ao abrir.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Abrir Caixa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Fechar Caixa -->
<?php if ($caixaAberto): ?>
<div class="modal fade" id="fecharCaixaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php?module=caixa&action=fechar_caixa" method="post">
                <input type="hidden" name="fechamento_id" value="<?php echo $caixaAberto['id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Fechar Caixa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Saldo Inicial:</strong> R$ <?php echo number_format($caixaAberto['saldo_inicial'], 2, ',', '.'); ?><br>
                        <strong>Entradas (Hoje):</strong> R$ <?php echo number_format($resumoDia['total_entradas'], 2, ',', '.'); ?><br>
                        <strong>Saídas (Hoje):</strong> R$ <?php echo number_format($resumoDia['total_saidas'], 2, ',', '.'); ?><br>
                        <hr>
                        <strong>Saldo Final Estimado:</strong> R$ <?php echo number_format($caixaAberto['saldo_inicial'] + $resumoDia['total_entradas'] - $resumoDia['total_saidas'], 2, ',', '.'); ?>
                    </div>
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre o fechamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Fechar Caixa</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true
    });

    $('.money').mask('#.##0,00', {reverse: true});
});
</script>
