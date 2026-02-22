<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-columns mr-2"></i>
            <?php echo empty($quadro['id']) ? 'Novo Quadro' : 'Editar Quadro'; ?>
        </h1>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['mensagem']['texto']; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['mensagem']); endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados do Quadro</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=kanban&action=salvar_quadro" method="POST">
                <?php if (!empty($quadro['id'])): ?>
                <input type="hidden" name="id" value="<?php echo $quadro['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nome">Nome do Quadro *</label>
                    <input type="text" class="form-control" id="nome" name="nome"
                           value="<?php echo htmlspecialchars($quadro['nome']); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="descricao">Descricao</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($quadro['descricao'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="cor">Cor do Quadro</label>
                    <div class="d-flex align-items-center">
                        <input type="color" class="form-control" id="cor" name="cor"
                               value="<?php echo htmlspecialchars($quadro['cor']); ?>" style="width: 60px; height: 40px; padding: 2px;">
                        <span class="ml-2 text-muted small">Cor da borda do quadro</span>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="index.php?module=kanban" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salvar Quadro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
