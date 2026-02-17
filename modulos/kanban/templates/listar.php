<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-columns mr-2"></i>Kanban</h1>
        <?php if (hasPermission('kanban_manage')): ?>
        <a href="index.php?module=kanban&action=novo_quadro" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm mr-1"></i> Novo Quadro
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['mensagem']['texto']; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['mensagem']); endif; ?>

    <?php if (empty($quadros)): ?>
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-columns text-gray-300" style="font-size: 64px;"></i>
            <p class="text-gray-600 mt-3 mb-0">Nenhum quadro criado ainda.</p>
            <?php if (hasPermission('kanban_manage')): ?>
            <a href="index.php?module=kanban&action=novo_quadro" class="btn btn-primary mt-3">
                <i class="fas fa-plus mr-1"></i> Criar primeiro quadro
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>

    <div class="row">
        <?php foreach ($quadros as $quadro): ?>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100" style="border-left: 4px solid <?php echo htmlspecialchars($quadro['cor']); ?>;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="font-weight-bold text-gray-800 mb-0">
                            <?php echo htmlspecialchars($quadro['nome']); ?>
                        </h5>
                        <?php if (hasPermission('kanban_manage')): ?>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow">
                                <a class="dropdown-item" href="index.php?module=kanban&action=editar_quadro&id=<?php echo $quadro['id']; ?>">
                                    <i class="fas fa-edit fa-sm mr-2 text-gray-400"></i>Editar
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="index.php?module=kanban&action=excluir_quadro&id=<?php echo $quadro['id']; ?>"
                                   onclick="return confirm('Excluir este quadro?');">
                                    <i class="fas fa-trash fa-sm mr-2"></i>Excluir
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($quadro['descricao'])): ?>
                    <p class="text-gray-600 small mb-3"><?php echo htmlspecialchars($quadro['descricao']); ?></p>
                    <?php endif; ?>

                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col-auto mr-3">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-columns mr-1"></i><?php echo $quadro['total_colunas']; ?> colunas
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-sticky-note mr-1"></i><?php echo $quadro['total_cards']; ?> cards
                            </span>
                        </div>
                    </div>

                    <a href="index.php?module=kanban&action=quadro&id=<?php echo $quadro['id']; ?>" class="btn btn-primary btn-block btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Abrir Quadro
                    </a>
                </div>
                <div class="card-footer bg-light py-2">
                    <small class="text-muted">
                        Atualizado: <?php echo date('d/m/Y H:i', strtotime($quadro['updated_at'])); ?>
                    </small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
