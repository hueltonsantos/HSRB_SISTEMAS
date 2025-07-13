<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">
        <?php echo isset($usuario) ? "Editar Usuário" : "Novo Usuário"; ?>
    </h1>
    
    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro']; 
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php echo isset($usuario) ? "Editar dados do usuário" : "Cadastrar novo usuário"; ?>
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?modulo=usuarios&action=salvar">
                <?php if (isset($usuario)): ?>
                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome *</label>
                            <input type="text" class="form-control" name="nome" required 
                                value="<?php echo isset($usuario) ? $usuario['nome'] : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>E-mail *</label>
                            <input type="email" class="form-control" name="email" required 
                                value="<?php echo isset($usuario) ? $usuario['email'] : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Senha <?php echo isset($usuario) ? '' : '*'; ?></label>
                            <input type="password" class="form-control" name="senha" 
                                <?php echo isset($usuario) ? '' : 'required'; ?>>
                            <?php if (isset($usuario)): ?>
                                <small class="form-text text-muted">Deixe em branco para manter a senha atual.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nível de Acesso *</label>
                            <select class="form-control" name="nivel_acesso" required>
                                <option value="">Selecione...</option>
                                <option value="admin" <?php echo (isset($usuario) && $usuario['nivel_acesso'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="recepcionista" <?php echo (isset($usuario) && $usuario['nivel_acesso'] == 'recepcionista') ? 'selected' : ''; ?>>Recepcionista</option>
                                <option value="medico" <?php echo (isset($usuario) && $usuario['nivel_acesso'] == 'medico') ? 'selected' : ''; ?>>Médico</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                                    <?php echo (!isset($usuario) || (isset($usuario) && $usuario['status'] == 1)) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="status">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="index.php?modulo=usuarios&action=listar" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>