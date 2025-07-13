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
            <h6 class="m-0 font-weight-bold text-primary">Dados do Procedimento</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=especialidades&action=save_procedimento" method="post" id="procedimentoForm">
                <!-- ID oculto para edição -->
                <?php if (isset($formData['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $formData['id']; ?>">
                <?php endif; ?>
                
                <!-- ID da especialidade -->
                <input type="hidden" name="especialidade_id" value="<?php echo $formData['especialidade_id']; ?>">
                
                <div class="row">
                    <!-- Nome do Procedimento -->
                    <div class="col-md-12 form-group">
                        <label for="procedimento">Nome do Procedimento</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['procedimento']) ? 'is-invalid' : ''; ?>" 
                            id="procedimento" name="procedimento" value="<?php echo isset($formData['procedimento']) ? htmlspecialchars($formData['procedimento']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['procedimento'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['procedimento']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Valor -->
                    <div class="col-md-6 form-group">
                        <label for="valor">Valor (R$)</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['valor']) ? 'is-invalid' : ''; ?>" 
                            id="valor" name="valor" value="<?php echo isset($formData['valor']) ? htmlspecialchars($formData['valor']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['valor'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['valor']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status -->
                    <div class="col-md-6 form-group">
                        <label for="status">Status</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                                <?php echo (!isset($formData['status']) || $formData['status'] == 1) ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="status">Ativo</label>
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
                    <a href="index.php?module=especialidades&action=procedimentos&id=<?php echo $formData['especialidade_id']; ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Máscara para campo de valor
    $('#valor').mask('#.##0,00', {reverse: true});
    
    // Validação do formulário
    $('#procedimentoForm').submit(function(e) {
        var valid = true;
        
        // Validar nome do procedimento (não pode estar vazio)
        var procedimento = $('#procedimento').val().trim();
        if (procedimento === '') {
            $('#procedimento').addClass('is-invalid');
            valid = false;
        } else {
            $('#procedimento').removeClass('is-invalid');
        }
        
        // Validar valor (não pode estar vazio e deve ser um número válido)
        var valor = $('#valor').val().trim();
        if (valor === '') {
            $('#valor').addClass('is-invalid');
            valid = false;
        } else {
            $('#valor').removeClass('is-invalid');
        }
        
        return valid;
    });
});
</script>