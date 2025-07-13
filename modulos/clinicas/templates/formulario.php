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
            <h6 class="m-0 font-weight-bold text-primary">Dados da Clínica</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=clinicas&action=save" method="post" id="clinicaForm">
                <!-- ID oculto para edição -->
                <?php if (isset($formData['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $formData['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <!-- Nome -->
                    <div class="col-md-6 form-group">
                        <label for="nome">Nome/Nome Fantasia</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['nome']) ? 'is-invalid' : ''; ?>" 
                            id="nome" name="nome" value="<?php echo isset($formData['nome']) ? htmlspecialchars($formData['nome']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['nome'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['nome']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Razão Social -->
                    <div class="col-md-6 form-group">
                        <label for="razao_social">Razão Social</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['razao_social']) ? 'is-invalid' : ''; ?>" 
                            id="razao_social" name="razao_social" 
                            value="<?php echo isset($formData['razao_social']) ? htmlspecialchars($formData['razao_social']) : ''; ?>">
                        <?php if (isset($formErrors['razao_social'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['razao_social']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row">
                    <!-- CNPJ -->
                    <div class="col-md-4 form-group">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['cnpj']) ? 'is-invalid' : ''; ?>" 
                            id="cnpj" name="cnpj" value="<?php echo isset($formData['cnpj']) ? htmlspecialchars($formData['cnpj']) : ''; ?>">
                        <?php if (isset($formErrors['cnpj'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['cnpj']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Responsável -->
                    <div class="col-md-8 form-group">
                        <label for="responsavel">Responsável</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['responsavel']) ? 'is-invalid' : ''; ?>" 
                            id="responsavel" name="responsavel" 
                            value="<?php echo isset($formData['responsavel']) ? htmlspecialchars($formData['responsavel']) : ''; ?>">
                        <?php if (isset($formErrors['responsavel'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['responsavel']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h5 class="mt-4 mb-3">Endereço</h5>
                
                <div class="row">
                    <!-- CEP -->
                    <div class="col-md-3 form-group">
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
                    <div class="col-md-6 form-group">
                        <label for="endereco">Endereço</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['endereco']) ? 'is-invalid' : ''; ?>" 
                            id="endereco" name="endereco" value="<?php echo isset($formData['endereco']) ? htmlspecialchars($formData['endereco']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['endereco'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['endereco']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Número -->
                    <div class="col-md-3 form-group">
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
                    <div class="col-md-4 form-group">
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
                    <div class="col-md-4 form-group">
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
                    <div class="col-md-3 form-group">
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
                    <div class="col-md-1 form-group">
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
                    <!-- Telefone -->
                    <div class="col-md-4 form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['telefone']) ? 'is-invalid' : ''; ?>" 
                            id="telefone" name="telefone" value="<?php echo isset($formData['telefone']) ? htmlspecialchars($formData['telefone']) : ''; ?>" 
                            required>
                        <?php if (isset($formErrors['telefone'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['telefone']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Celular -->
                    <div class="col-md-4 form-group">
                        <label for="celular">Celular</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['celular']) ? 'is-invalid' : ''; ?>" 
                            id="celular" name="celular" value="<?php echo isset($formData['celular']) ? htmlspecialchars($formData['celular']) : ''; ?>">
                        <?php if (isset($formErrors['celular'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['celular']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-4 form-group">
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
                
                <div class="row">
                    <!-- Site -->
                    <div class="col-md-12 form-group">
                        <label for="site">Site</label>
                        <input type="url" class="form-control <?php echo isset($formErrors['site']) ? 'is-invalid' : ''; ?>" 
                            id="site" name="site" value="<?php echo isset($formData['site']) ? htmlspecialchars($formData['site']) : ''; ?>"
                            placeholder="https://www.exemplo.com.br">
                        <?php if (isset($formErrors['site'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['site']; ?>
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
                    <a href="index.php?module=clinicas&action=list" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para máscaras e validações -->
<script>
$(document).ready(function(){
    // Máscaras para os campos
    $('#cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('#cep').mask('00000-000');
    $('#telefone').mask('(00) 0000-0000');
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
    $('#clinicaForm').submit(function(e) {
        var valid = true;
        
        // Validação básica do CNPJ se fornecido
        var cnpj = $('#cnpj').val().replace(/\D/g, '');
        if (cnpj !== '' && cnpj.length !== 14) {
            $('#cnpj').addClass('is-invalid');
            valid = false;
        } else {
            $('#cnpj').removeClass('is-invalid');
        }
        
        return valid;
    });
});
</script>