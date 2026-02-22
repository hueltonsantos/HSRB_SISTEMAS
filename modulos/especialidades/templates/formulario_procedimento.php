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
                    <!-- Valor Paciente -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="valor_paciente">Valor Paciente (R$)</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['valor_paciente']) ? 'is-invalid' : ''; ?>" 
                            id="valor_paciente" name="valor_paciente" value="<?php echo isset($formData['valor_paciente']) ? htmlspecialchars($formData['valor_paciente']) : ''; ?>" 
                            required>
                        <small class="form-text text-muted">Valor cobrado do paciente.</small>
                        <?php if (isset($formErrors['valor_paciente'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['valor_paciente']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Valor Clínica (Repasse/Custo) -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="valor_repasse">Valor Clínica (Custo)</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['valor_repasse']) ? 'is-invalid' : ''; ?>" 
                            id="valor_repasse" name="valor_repasse" value="<?php echo isset($formData['valor_repasse']) ? htmlspecialchars($formData['valor_repasse']) : ''; ?>" 
                            required>
                        <small class="form-text text-muted">Custo para a clínica (ex: repasse ao médico).</small>
                        <?php if (isset($formErrors['valor_repasse'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['valor_repasse']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Lucro Previsto (Calculado) -->
                    <div class="col-sm-12 col-md-4 form-group">
                        <label for="lucro_previsto">Lucro Previsto</label>
                        <input type="text" class="form-control" id="lucro_previsto" readonly>
                        <small class="form-text text-muted">Cálculo automático: Paciente - Custo.</small>
                    </div>
                </div>

                <div class="row">
                    <!-- Status -->
                    <div class="col-md-12 form-group">
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
    // Máscara para campos de valor
    $('#valor_paciente, #valor_repasse').mask('#.##0,00', {reverse: true});
    
    // Calcula lucro previste
    function calcularLucro() {
        var valorPaciente = $('#valor_paciente').val().replace(/\./g, '').replace(',', '.');
        var valorRepasse = $('#valor_repasse').val().replace(/\./g, '').replace(',', '.');
        
        valorPaciente = parseFloat(valorPaciente) || 0;
        valorRepasse = parseFloat(valorRepasse) || 0;
        
        var lucro = valorPaciente - valorRepasse;
        
        // Formata para exibição (R$)
        var lucroFormatado = lucro.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        $('#lucro_previsto').val('R$ ' + lucroFormatado);
        
        // Colore conforme o lucro
        if (lucro > 0) {
            $('#lucro_previsto').removeClass('text-danger').addClass('text-success font-weight-bold');
        } else if (lucro < 0) {
            $('#lucro_previsto').removeClass('text-success').addClass('text-danger font-weight-bold');
        } else {
            $('#lucro_previsto').removeClass('text-success text-danger').addClass('font-weight-bold');
        }
    }
    
    // Eventos para cálculo
    $('#valor_paciente, #valor_repasse').on('keyup change', function() {
        calcularLucro();
    });
    
    // Calcula ao carregar
    calcularLucro();
    
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
        
        // Validar valor paciente
        var valorPaciente = $('#valor_paciente').val().trim();
        if (valorPaciente === '') {
            $('#valor_paciente').addClass('is-invalid');
            valid = false;
        } else {
            $('#valor_paciente').removeClass('is-invalid');
        }

        // Validar valor repasse
        var valorRepasse = $('#valor_repasse').val().trim();
        if (valorRepasse === '') {
            $('#valor_repasse').addClass('is-invalid');
            valid = false;
        } else {
            $('#valor_repasse').removeClass('is-invalid');
        }
        
        return valid;
    });
});
</script>