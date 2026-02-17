<?php
// Se for o próprio perfil, esconde campos sensíveis
$isMyProfile = isset($isMyProfile) && $isMyProfile === true;
?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">
        <?php echo $isMyProfile ? "Meu Perfil" : (isset($usuario) ? "Editar Usuário" : "Novo Usuário"); ?>
    </h1>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php
            echo $_SESSION['erro'];
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success">
            <?php
            echo $_SESSION['sucesso'];
            unset($_SESSION['sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php echo $isMyProfile ? "Editar meus dados" : (isset($usuario) ? "Editar dados do usuário" : "Cadastrar novo usuário"); ?>
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?module=usuarios&action=<?php echo $isMyProfile ? 'salvar_perfil' : 'salvar'; ?>" enctype="multipart/form-data">
                <?php if (isset($usuario)): ?>
                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                <?php endif; ?>

                <?php if ($isMyProfile): ?>
                <!-- Seção de Foto do Perfil -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center">
                            <div class="mr-4">
                                <?php
                                $fotoPath = isset($usuario['foto']) && $usuario['foto'] ? 'uploads/usuarios/' . $usuario['foto'] : 'assents/img/user.png';
                                ?>
                                <img src="<?php echo $fotoPath; ?>" class="rounded-circle" width="100" height="100" style="object-fit: cover;" id="preview-foto">
                            </div>
                            <div>
                                <label class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-camera"></i> Alterar Foto
                                    <input type="file" name="foto" accept="image/*" style="display: none;" onchange="previewImage(this)">
                                </label>
                                <small class="d-block text-muted mt-1">JPG, PNG. Máximo 2MB.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <div class="form-group">
                            <label>Nome *</label>
                            <input type="text" class="form-control" name="nome" required
                                value="<?php echo isset($usuario) ? htmlspecialchars($usuario['nome']) : ''; ?>">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="form-group">
                            <label>E-mail *</label>
                            <input type="email" class="form-control" name="email" required
                                value="<?php echo isset($usuario) ? htmlspecialchars($usuario['email']) : ''; ?>">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Senha <?php echo isset($usuario) ? '' : '*'; ?></label>
                            <input type="password" class="form-control" name="senha"
                                <?php echo isset($usuario) ? '' : 'required'; ?>>
                            <?php if (isset($usuario)): ?>
                                <small class="form-text text-muted">Deixe vazio para manter a senha atual.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!$isMyProfile): ?>
                <!-- Campos administrativos - só aparecem para admins editando outros usuários -->
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <div class="form-group">
                            <label>Perfil de Acesso *</label>
                            <select class="form-control" name="perfil_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($perfis as $perfil): ?>
                                    <option value="<?php echo $perfil['id']; ?>"
                                        <?php echo (isset($usuario) && $usuario['perfil_id'] == $perfil['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($perfil['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="form-group">
                            <label>Clínica (Se vinculado)</label>
                            <select class="form-control" name="clinica_id">
                                <option value="">Acesso Geral (Todas as Clínicas)</option>
                                <?php foreach ($clinicas as $clinica): ?>
                                    <option value="<?php echo $clinica['id']; ?>"
                                        <?php echo (isset($usuario) && $usuario['clinica_id'] == $clinica['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($clinica['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Supervisor (Hierarquia)</label>
                            <select class="form-control" name="parent_id">
                                <option value="">Nenhum (Topo da Hierarquia)</option>
                                <?php foreach ($supervisores as $supervisor): ?>
                                    <option value="<?php echo $supervisor['id']; ?>"
                                        <?php echo (isset($usuario) && $usuario['parent_id'] == $supervisor['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supervisor['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
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
                <?php endif; ?>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <a href="<?php echo $isMyProfile ? 'index.php?module=dashboard' : 'index.php?module=usuarios&action=listar'; ?>" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($isMyProfile): ?>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-foto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php endif; ?>
