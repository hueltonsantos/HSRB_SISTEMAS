<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bell mr-2"></i>Notificações
        </h1>
        <?php if ($totalNaoLidas > 0): ?>
            <a href="index.php?module=sistema&action=notificacoes&acao=marcar_todas"
               class="btn btn-sm btn-outline-primary"
               onclick="return confirm('Marcar todas as notificações como lidas?')">
                <i class="fas fa-check-double mr-1"></i> Marcar todas como lidas
                <span class="badge badge-primary ml-1"><?php echo $totalNaoLidas; ?></span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="index.php?module=sistema&action=notificacoes&filtro=todas"
               class="btn btn-sm <?php echo $filtro === 'todas' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Todas <span class="badge badge-light ml-1"><?php echo $total; ?></span>
            </a>
            <a href="index.php?module=sistema&action=notificacoes&filtro=nao_lidas"
               class="btn btn-sm <?php echo $filtro === 'nao_lidas' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Não lidas <span class="badge badge-light ml-1"><?php echo $totalNaoLidas; ?></span>
            </a>
        </div>
    </div>

    <!-- Lista de Notificações -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <?php if (empty($notificacoes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500 mb-0">Nenhuma notificação encontrada.</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <div class="list-group-item list-group-item-action d-flex align-items-center py-3 <?php echo $notificacao['lida'] ? '' : 'bg-light border-left-primary'; ?>">
                            <!-- Ícone -->
                            <div class="mr-3">
                                <div class="icon-circle bg-<?php echo htmlspecialchars($notificacao['cor']); ?>" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-<?php echo htmlspecialchars($notificacao['icone']); ?> text-white"></i>
                                </div>
                            </div>

                            <!-- Conteúdo -->
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="font-weight-bold text-gray-800">
                                            <?php echo htmlspecialchars($notificacao['titulo']); ?>
                                        </span>
                                        <?php if (!$notificacao['lida']): ?>
                                            <span class="badge badge-primary badge-pill ml-1" style="font-size: 0.6rem;">Nova</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-gray-500 ml-3 text-nowrap">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php echo $notificacaoModel->formatarDataNotificacao($notificacao['data_criacao']); ?>
                                    </small>
                                </div>
                                <?php if (!empty($notificacao['mensagem'])): ?>
                                    <div class="text-gray-600 small mt-1">
                                        <?php echo htmlspecialchars($notificacao['mensagem']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Ações -->
                            <div class="ml-3 d-flex align-items-center">
                                <?php if (!empty($notificacao['link'])): ?>
                                    <a href="<?php echo htmlspecialchars($notificacao['link']); ?>" class="btn btn-sm btn-outline-info mr-1" title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$notificacao['lida']): ?>
                                    <a href="index.php?module=sistema&action=notificacoes&acao=marcar_lida&id=<?php echo $notificacao['id']; ?>&redirect=<?php echo urlencode('index.php?module=sistema&action=notificacoes&filtro=' . $filtro . '&pagina=' . $pagina); ?>"
                                       class="btn btn-sm btn-outline-success" title="Marcar como lida">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-gray-600">
                        Exibindo <?php echo count($notificacoes); ?> de <?php echo $total; ?> notificações
                    </small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Anterior -->
                            <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?module=sistema&action=notificacoes&filtro=<?php echo $filtro; ?>&pagina=<?php echo $pagina - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <?php
                            $inicio = max(1, $pagina - 2);
                            $fim = min($totalPaginas, $pagina + 2);
                            if ($inicio > 1): ?>
                                <li class="page-item"><a class="page-link" href="index.php?module=sistema&action=notificacoes&filtro=<?php echo $filtro; ?>&pagina=1">1</a></li>
                                <?php if ($inicio > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                                <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?module=sistema&action=notificacoes&filtro=<?php echo $filtro; ?>&pagina=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($fim < $totalPaginas): ?>
                                <?php if ($fim < $totalPaginas - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="index.php?module=sistema&action=notificacoes&filtro=<?php echo $filtro; ?>&pagina=<?php echo $totalPaginas; ?>"><?php echo $totalPaginas; ?></a></li>
                            <?php endif; ?>

                            <!-- Próximo -->
                            <li class="page-item <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?module=sistema&action=notificacoes&filtro=<?php echo $filtro; ?>&pagina=<?php echo $pagina + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
