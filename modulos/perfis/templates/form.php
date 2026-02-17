<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?php echo $id ? 'Editar Perfil' : 'Novo Perfil'; ?></h1>
    <a href="index.php?module=perfis" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Voltar
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Dados do Perfil</h6>
    </div>
    <div class="card-body">
        <form action="index.php?module=perfis&action=save" method="POST">
            <input type="hidden" name="id" value="<?php echo $perfil['id']; ?>">
            
            <div class="form-group">
                <label for="nome">Nome do Perfil</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($perfil['nome']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($perfil['descricao']); ?></textarea>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?php echo $perfil['status'] ? 'checked' : ''; ?>>
                    <label class="custom-control-label" for="status">Ativo</label>
                </div>
            </div>

            <hr>
            <h6 class="font-weight-bold text-primary mb-3">Permissões de Acesso</h6>
            
            <div class="row">
                <?php foreach ($allPermissions as $perm): ?>
                    <div class="col-sm-6 col-md-4 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" 
                                   id="perm_<?php echo $perm['id']; ?>" 
                                   name="permissions[]" 
                                   value="<?php echo $perm['id']; ?>"
                                   <?php echo in_array($perm['id'], $selectedPermissions) ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="perm_<?php echo $perm['id']; ?>">
                                <?php echo htmlspecialchars($perm['nome']); ?>
                                <small class="d-block text-muted"><?php echo htmlspecialchars($perm['descricao']); ?></small>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
