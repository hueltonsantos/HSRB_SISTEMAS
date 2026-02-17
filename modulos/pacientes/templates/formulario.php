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
            <h6 class="m-0 font-weight-bold text-primary">Dados do Paciente</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=pacientes&action=save" method="post" id="pacienteForm">
                <!-- ID oculto para edição -->
                <?php if (isset($formData['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $formData['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <!-- Nome -->
                    <div class="col-sm-12 col-md-8 form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['nome']) ? 'is-invalid' : ''; ?>" 
                            id="nome" name="nome" value="<?php echo isset($formData['nome']) ? htmlspecialchars($formData['nome']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['nome'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['nome']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Data de Nascimento -->
                    <div class="col-sm-12 col-md-4 form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['data_nascimento']) ? 'is-invalid' : ''; ?>" 
                            id="data_nascimento" name="data_nascimento" 
                            value="<?php echo isset($formData['data_nascimento']) ? htmlspecialchars($formData['data_nascimento']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['data_nascimento'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['data_nascimento']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- CPF -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['cpf']) ? 'is-invalid' : ''; ?>" 
                            id="cpf" name="cpf" value="<?php echo isset($formData['cpf']) ? htmlspecialchars($formData['cpf']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['cpf'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['cpf']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- RG -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="rg">RG</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['rg']) ? 'is-invalid' : ''; ?>" 
                            id="rg" name="rg" value="<?php echo isset($formData['rg']) ? htmlspecialchars($formData['rg']) : ''; ?>">
                        <?php if (isset($formErrors['rg'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['rg']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sexo -->
                    <div class="col-sm-12 col-md-4 form-group">
                        <label for="sexo">Sexo</label>
                        <select class="form-control <?php echo isset($formErrors['sexo']) ? 'is-invalid' : ''; ?>" 
                            id="sexo" name="sexo" required>
                            <option value="">Selecione...</option>
                            <option value="M" <?php echo (isset($formData['sexo']) && $formData['sexo'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="F" <?php echo (isset($formData['sexo']) && $formData['sexo'] == 'F') ? 'selected' : ''; ?>>Feminino</option>
                            <option value="O" <?php echo (isset($formData['sexo']) && $formData['sexo'] == 'O') ? 'selected' : ''; ?>>Outro</option>
                        </select>
                        <?php if (isset($formErrors['sexo'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['sexo']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h5 class="mt-4 mb-3">Endereço</h5>
                
                <div class="row">
                    <!-- CEP -->
                    <div class="col-sm-6 col-md-3 form-group">
                        <label for="cep">CEP</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['cep']) ? 'is-invalid' : ''; ?>" 
                            id="cep" name="cep" value="<?php echo isset($formData['cep']) ? htmlspecialchars($formData['cep']) : ''; ?>">
                        <?php if (isset($formErrors['cep'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['cep']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="col-sm-12 col-md-6 form-group">
                        <label for="endereco">Endereço</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['endereco']) ? 'is-invalid' : ''; ?>" 
                            id="endereco" name="endereco" value="<?php echo isset($formData['endereco']) ? htmlspecialchars($formData['endereco']) : ''; ?>">
                        <?php if (isset($formErrors['endereco'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['endereco']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Número -->
                    <div class="col-sm-6 col-md-3 form-group">
                        <label for="numero">Número</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['numero']) ? 'is-invalid' : ''; ?>" 
                            id="numero" name="numero" value="<?php echo isset($formData['numero']) ? htmlspecialchars($formData['numero']) : ''; ?>">
                        <?php if (isset($formErrors['numero'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['numero']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Complemento -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['complemento']) ? 'is-invalid' : ''; ?>" 
                            id="complemento" name="complemento" value="<?php echo isset($formData['complemento']) ? htmlspecialchars($formData['complemento']) : ''; ?>">
                        <?php if (isset($formErrors['complemento'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['complemento']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Bairro -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="bairro">Bairro</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['bairro']) ? 'is-invalid' : ''; ?>" 
                            id="bairro" name="bairro" value="<?php echo isset($formData['bairro']) ? htmlspecialchars($formData['bairro']) : ''; ?>">
                        <?php if (isset($formErrors['bairro'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['bairro']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Cidade -->
                    <div class="col-sm-6 col-md-3 form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['cidade']) ? 'is-invalid' : ''; ?>" 
                            id="cidade" name="cidade" value="<?php echo isset($formData['cidade']) ? htmlspecialchars($formData['cidade']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['cidade'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['cidade']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Estado -->
                    <div class="col-sm-6 col-md-1 form-group">
                        <label for="estado">UF</label>
                        <select class="form-control <?php echo isset($formErrors['estado']) ? 'is-invalid' : ''; ?>" 
                            id="estado" name="estado" required>
                            <option value="">UF</option>
                            <?php foreach ($estados as $sigla => $nome): ?>
                                <option value="<?php echo $sigla; ?>" <?php echo (isset($formData['estado']) && $formData['estado'] == $sigla) ? 'selected' : ''; ?>>
                                    <?php echo $sigla; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($formErrors['estado'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['estado']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h5 class="mt-4 mb-3">Contato</h5>
                
                <div class="row">
                    <!-- Telefone Fixo -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="telefone_fixo">Telefone Fixo</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['telefone_fixo']) ? 'is-invalid' : ''; ?>" 
                            id="telefone_fixo" name="telefone_fixo" value="<?php echo isset($formData['telefone_fixo']) ? htmlspecialchars($formData['telefone_fixo']) : ''; ?>">
                        <?php if (isset($formErrors['telefone_fixo'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['telefone_fixo']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Celular -->
                    <div class="col-sm-6 col-md-4 form-group">
                        <label for="celular">Celular</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['celular']) ? 'is-invalid' : ''; ?>" 
                            id="celular" name="celular" value="<?php echo isset($formData['celular']) ? htmlspecialchars($formData['celular']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['celular'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['celular']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-sm-12 col-md-4 form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control <?php echo isset($formErrors['email']) ? 'is-invalid' : ''; ?>" 
                            id="email" name="email" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>">
                        <?php if (isset($formErrors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['email']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h5 class="mt-4 mb-3">Convênio</h5>
                
                <div class="row">
                    <!-- Convênio -->
                    <div class="col-sm-6 col-md-6 form-group">
                        <label for="convenio">Convênio</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['convenio']) ? 'is-invalid' : ''; ?>" 
                            id="convenio" name="convenio" value="<?php echo isset($formData['convenio']) ? htmlspecialchars($formData['convenio']) : ''; ?>">
                        <?php if (isset($formErrors['convenio'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['convenio']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Número da Carteirinha -->
                    <div class="col-sm-6 col-md-6 form-group">
                        <label for="numero_carteirinha">Número da Carteirinha</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['numero_carteirinha']) ? 'is-invalid' : ''; ?>" 
                            id="numero_carteirinha" name="numero_carteirinha" value="<?php echo isset($formData['numero_carteirinha']) ? htmlspecialchars($formData['numero_carteirinha']) : ''; ?>">
                        <?php if (isset($formErrors['numero_carteirinha'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['numero_carteirinha']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Observações -->
                    <div class="col-md-12 form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control <?php echo isset($formErrors['observacoes']) ? 'is-invalid' : ''; ?>" 
                            id="observacoes" name="observacoes" rows="3"><?php echo isset($formData['observacoes']) ? htmlspecialchars($formData['observacoes']) : ''; ?></textarea>
                        <?php if (isset($formErrors['observacoes'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['observacoes']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Status -->
                    <div class="col-md-12 form-group">
                        <label for="status">Status</label>
                        <div class="custom-control custom-switch">
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
                    <a href="index.php?module=pacientes&action=list" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para máscaras e validações -->
<script>
$(document).ready(function(){
    // Máscaras para os campos
    $('#cpf').mask('000.000.000-00', {reverse: true});
    $('#cep').mask('00000-000');
    $('#data_nascimento').mask('00/00/0000');
    $('#telefone_fixo').mask('(00) 0000-0000');
    $('#celular').mask('(00) 00000-0000');
    
    // Consultando CEP para preenchimento de endereço
    $('#cep').blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length === 8) {
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                    $('#numero').focus();
                }
            });
        }
    });
    
    // Validação do formulário
    $('#pacienteForm').submit(function(e) {
        var valid = true;
        
        // Validação básica do CPF
        var cpf = $('#cpf').val().replace(/\D/g, '');
        if (cpf.length !== 11) {
            $('#cpf').addClass('is-invalid');
            valid = false;
        } else {
            $('#cpf').removeClass('is-invalid');
        }
        
        // Validação da data de nascimento
        var dataNascimento = $('#data_nascimento').val();
        if (dataNascimento.length === 10) {
            var dataParts = dataNascimento.split('/');
            var day = parseInt(dataParts[0], 10);
            var month = parseInt(dataParts[1], 10);
            var year = parseInt(dataParts[2], 10);
            
            var date = new Date(year, month - 1, day);
            
            if (date.getFullYear() !== year || date.getMonth() + 1 !== month || date.getDate() !== day) {
                $('#data_nascimento').addClass('is-invalid');
                valid = false;
            } else {
                $('#data_nascimento').removeClass('is-invalid');
            }
        } else {
            $('#data_nascimento').addClass('is-invalid');
            valid = false;
        }
        
        return valid;
    });
});
</script>