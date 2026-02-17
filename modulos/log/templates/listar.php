<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-history"></i> Log de Atividades
    </h1>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="module" value="log">
                <div class="row">
                    <div class="col-sm-4 col-md-2">
                        <div class="form-group">
                            <label>Usuário</label>
                            <select class="form-control" name="usuario_id">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?php echo $u['id']; ?>" <?php echo $filtros['usuario_id'] == $u['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($u['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <div class="form-group">
                            <label>Ação</label>
                            <select class="form-control" name="acao">
                                <option value="">Todas</option>
                                <?php foreach ($acoes as $a): ?>
                                    <option value="<?php echo $a['acao']; ?>" <?php echo $filtros['acao'] == $a['acao'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($a['acao']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <div class="form-group">
                            <label>Módulo</label>
                            <select class="form-control" name="modulo">
                                <option value="">Todos</option>
                                <?php foreach ($modulos as $m): ?>
                                    <option value="<?php echo $m['modulo']; ?>" <?php echo $filtros['modulo'] == $m['modulo'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['modulo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <div class="form-group">
                            <label>Data Início</label>
                            <input type="date" class="form-control" name="data_inicio" value="<?php echo $filtros['data_inicio']; ?>">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <div class="form-group">
                            <label>Data Fim</label>
                            <input type="date" class="form-control" name="data_fim" value="<?php echo $filtros['data_fim']; ?>">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="index.php?module=log" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Logs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Registros (<?php echo number_format($total, 0, ',', '.'); ?> encontrados)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="140">Data/Hora</th>
                            <th width="120">Usuário</th>
                            <th width="80">Ação</th>
                            <th width="100">Módulo</th>
                            <th>Descrição</th>
                            <th width="110">IP</th>
                            <th width="60">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum registro encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="small"><?php echo date('d/m/Y H:i:s', strtotime($log['data_hora'])); ?></td>
                                <td><?php echo htmlspecialchars($log['usuario_nome'] ?? 'Sistema'); ?></td>
                                <td>
                                    <?php
                                    $badgeClass = 'secondary';
                                    switch ($log['acao']) {
                                        case 'criar': $badgeClass = 'success'; break;
                                        case 'editar': $badgeClass = 'warning'; break;
                                        case 'excluir': $badgeClass = 'danger'; break;
                                        case 'login': $badgeClass = 'info'; break;
                                        case 'logout': $badgeClass = 'dark'; break;
                                    }
                                    ?>
                                    <span class="badge badge-<?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($log['acao']); ?>
                                    </span>
                                </td>
                                <td><span class="badge badge-light"><?php echo htmlspecialchars($log['modulo']); ?></span></td>
                                <td class="small"><?php echo htmlspecialchars($log['descricao']); ?></td>
                                <td class="small"><?php echo htmlspecialchars($log['ip']); ?></td>
                                <td class="text-center">
                                    <?php if ($log['dados_anteriores'] || $log['dados_novos']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalLog<?php echo $log['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Paginação">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?module=log&pagina=<?php echo $pagina - 1; ?>&<?php echo http_build_query($filtros); ?>">Anterior</a>
                    </li>
                    <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                            <a class="page-link" href="?module=log&pagina=<?php echo $i; ?>&<?php echo http_build_query($filtros); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?module=log&pagina=<?php echo $pagina + 1; ?>&<?php echo http_build_query($filtros); ?>">Próximo</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modals para detalhes -->
<?php foreach ($logs as $log): ?>
<?php if ($log['dados_anteriores'] || $log['dados_novos']): ?>
<div class="modal fade" id="modalLog<?php echo $log['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Log #<?php echo $log['id']; ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php if ($log['dados_anteriores']): ?>
                    <div class="col-md-6">
                        <h6 class="text-danger"><i class="fas fa-minus-circle"></i> Dados Anteriores</h6>
                        <pre class="bg-light p-2 small" style="max-height: 300px; overflow: auto;"><?php echo json_encode(json_decode($log['dados_anteriores']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                    </div>
                    <?php endif; ?>
                    <?php if ($log['dados_novos']): ?>
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="fas fa-plus-circle"></i> Dados Novos</h6>
                        <pre class="bg-light p-2 small" style="max-height: 300px; overflow: auto;"><?php echo json_encode(json_decode($log['dados_novos']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                    </div>
                    <?php endif; ?>
                </div>
                <hr>
                <small class="text-muted">
                    <strong>User Agent:</strong> <?php echo htmlspecialchars($log['user_agent'] ?? '-'); ?>
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
