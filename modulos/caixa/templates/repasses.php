<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Repasses</h1>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Repasses para Clínicas Parceiras</h6>
            <div>
                <a href="index.php?module=caixa&action=listar" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Voltar ao Caixa
                </a>
                <?php if (hasPermission('repasse_manage')): ?>
                <a href="index.php?module=caixa&action=gerar_repasse" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Gerar Repasse
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="mb-4">
                <form action="index.php" method="get" class="form">
                    <input type="hidden" name="module" value="caixa">
                    <input type="hidden" name="action" value="repasses">

                    <div class="row mb-3">
                        <div class="col-sm-6 col-md-3">
                            <label for="clinica_id">Clínica</label>
                            <select class="form-control" id="clinica_id" name="clinica_id">
                                <option value="">Todas</option>
                                <?php foreach ($clinicas as $clinica): ?>
                                    <option value="<?php echo $clinica['id']; ?>" <?php echo (isset($_GET['clinica_id']) && $_GET['clinica_id'] == $clinica['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($clinica['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="pendente" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                                <option value="parcial" <?php echo (isset($_GET['status']) && $_GET['status'] == 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                                <option value="pago" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pago') ? 'selected' : ''; ?>>Pago</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="periodo_inicio">Período Início</label>
                            <input type="text" class="form-control datepicker" id="periodo_inicio" name="periodo_inicio"
                                value="<?php echo isset($_GET['periodo_inicio']) ? htmlspecialchars($_GET['periodo_inicio']) : ''; ?>"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label for="periodo_fim">Período Fim</label>
                            <input type="text" class="form-control datepicker" id="periodo_fim" name="periodo_fim"
                                value="<?php echo isset($_GET['periodo_fim']) ? htmlspecialchars($_GET['periodo_fim']) : ''; ?>"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <div class="col-sm-6 col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mb-2 mr-2">Filtrar</button>
                            <a href="index.php?module=caixa&action=repasses" class="btn btn-secondary mb-2">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabela de Repasses -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Clínica</th>
                            <th>Período</th>
                            <th>Valor Total</th>
                            <th>Valor Pago</th>
                            <th>Saldo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($repasses)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum repasse encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($repasses as $repasse): ?>
                                <tr>
                                    <td><?php echo $repasse['id']; ?></td>
                                    <td><?php echo htmlspecialchars($repasse['clinica_nome']); ?></td>
                                    <td>
                                        <?php echo $caixaModel->formatDateForDisplay($repasse['periodo_inicio']); ?>
                                        a <?php echo $caixaModel->formatDateForDisplay($repasse['periodo_fim']); ?>
                                    </td>
                                    <td class="text-right">R$ <?php echo number_format($repasse['valor_total'], 2, ',', '.'); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($repasse['valor_pago'], 2, ',', '.'); ?></td>
                                    <td class="text-right font-weight-bold">
                                        R$ <?php echo number_format($repasse['valor_total'] - $repasse['valor_pago'], 2, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'secondary';
                                        $statusLabel = ucfirst($repasse['status']);
                                        if ($repasse['status'] === 'pendente') $statusClass = 'warning';
                                        elseif ($repasse['status'] === 'parcial') $statusClass = 'info';
                                        elseif ($repasse['status'] === 'pago') $statusClass = 'success';
                                        ?>
                                        <span class="badge badge-<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                    </td>
                                    <td>
                                        <a href="index.php?module=caixa&action=visualizar_repasse&id=<?php echo $repasse['id']; ?>"
                                            class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
                                    <a class="page-link" href="index.php?module=caixa&action=repasses&page=<?php echo $page - 1; ?><?php echo isset($_GET['clinica_id']) ? '&clinica_id=' . $_GET['clinica_id'] : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                                        Anterior
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?module=caixa&action=repasses&page=<?php echo $i; ?><?php echo isset($_GET['clinica_id']) ? '&clinica_id=' . $_GET['clinica_id'] : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=caixa&action=repasses&page=<?php echo $page + 1; ?><?php echo isset($_GET['clinica_id']) ? '&clinica_id=' . $_GET['clinica_id'] : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                                        Próximo
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

            <div class="mt-3 text-muted">
                <small>Exibindo <?php echo count($repasses); ?> de <?php echo $totalRepasses; ?> repasses.</small>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true
    });
});
</script>
