<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $pageTitle; ?></h1>
    
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados da Especialidade</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=especialidades&action=save" method="post" id="especialidadeForm">
                <!-- ID oculto para edição -->
                <?php if (isset($formData['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $formData['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <!-- Nome -->
                    <div class="col-md-12 form-group">
                        <label for="nome">Nome da Especialidade</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['nome']) ? 'is-invalid' : ''; ?>" 
                            id="nome" name="nome" value="<?php echo isset($formData['nome']) ? htmlspecialchars($formData['nome']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['nome'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['nome']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Descrição -->
                    <div class="col-md-12 form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control <?php echo isset($formErrors['descricao']) ? 'is-invalid' : ''; ?>" 
                            id="descricao" name="descricao" rows="4"><?php echo isset($formData['descricao']) ? htmlspecialchars($formData['descricao']) : ''; ?></textarea>
                        <?php if (isset($formErrors['descricao'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['descricao']; ?>
                            </div>
                        <?php endif; ?>
                        <small class="form-text text-muted">Descreva brevemente esta especialidade (opcional).</small>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Status -->
                    <div class="col-md-12 form-group">
                        <label for="status">Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                                <?php echo (!isset($formData['status']) || $formData['status'] == 1) ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="status">Ativa</label>
                        </div>
                        <?php if (isset($formErrors['status'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo $formErrors['status']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="index.php?module=especialidades&action=list" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Validação do formulário
    $('#especialidadeForm').submit(function(e) {
        var valid = true;
        
        // Validar nome (não pode estar vazio)
        var nome = $('#nome').val().trim();
        if (nome === '') {
            $('#nome').addClass('is-invalid');
            valid = false;
        } else {
            $('#nome').removeClass('is-invalid');
        }
        
        return valid;
    });
});
</script>