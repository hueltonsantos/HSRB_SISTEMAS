<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Novo Lançamento</h1>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <?php if (!$caixaAberto): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> O caixa está fechado. Abra o caixa antes de fazer lançamentos.
            <a href="index.php?module=caixa&action=listar" class="btn btn-sm btn-warning ml-2">Voltar ao Caixa</a>
        </div>
    <?php else: ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados do Lançamento</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=caixa&action=salvar_lancamento" method="post">
                <div class="row">
                    <div class="col-sm-12 col-md-3 form-group">
                        <label for="tipo">Tipo *</label>
                        <select class="form-control <?php echo isset($formErrors['tipo']) ? 'is-invalid' : ''; ?>" id="tipo" name="tipo" required>
                            <option value="entrada" <?php echo (isset($formData['tipo']) && $formData['tipo'] == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                            <option value="saida" <?php echo (isset($formData['tipo']) && $formData['tipo'] == 'saida') ? 'selected' : ''; ?>>Saída</option>
                        </select>
                        <?php if (isset($formErrors['tipo'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['tipo']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-12 col-md-3 form-group">
                        <label for="data">Data *</label>
                        <input type="text" class="form-control datepicker <?php echo isset($formErrors['data']) ? 'is-invalid' : ''; ?>"
                            id="data" name="data"
                            value="<?php echo htmlspecialchars($formData['data'] ?? date('d/m/Y')); ?>"
                            placeholder="DD/MM/AAAA" required>
                        <?php if (isset($formErrors['data'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['data']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-12 col-md-3 form-group">
                        <label for="valor">Valor (R$) *</label>
                        <input type="text" class="form-control money <?php echo isset($formErrors['valor']) ? 'is-invalid' : ''; ?>"
                            id="valor" name="valor"
                            value="<?php echo htmlspecialchars($formData['valor'] ?? ''); ?>"
                            placeholder="0,00" required>
                        <?php if (isset($formErrors['valor'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['valor']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-12 col-md-3 form-group">
                        <label for="forma_pagamento">Forma de Pagamento *</label>
                        <select class="form-control <?php echo isset($formErrors['forma_pagamento']) ? 'is-invalid' : ''; ?>"
                            id="forma_pagamento" name="forma_pagamento" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($formasPagamento as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($formData['forma_pagamento']) && $formData['forma_pagamento'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($formErrors['forma_pagamento'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['forma_pagamento']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-4 form-group">
                        <label for="categoria">Categoria</label>
                        <input type="text" class="form-control" id="categoria" name="categoria"
                            value="<?php echo htmlspecialchars($formData['categoria'] ?? ''); ?>"
                            placeholder="Ex: Consulta, Material, Aluguel...">
                    </div>

                    <div class="col-sm-12 col-md-8 form-group">
                        <label for="descricao">Descrição *</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['descricao']) ? 'is-invalid' : ''; ?>"
                            id="descricao" name="descricao"
                            value="<?php echo htmlspecialchars($formData['descricao'] ?? ''); ?>"
                            placeholder="Descreva o lançamento" required>
                        <?php if (isset($formErrors['descricao'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['descricao']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6 form-group">
                        <label for="paciente_id">Paciente (opcional)</label>
                        <select class="form-control select2" id="paciente_id" name="paciente_id">
                            <option value="">Selecione...</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?php echo $paciente['id']; ?>" <?php echo (isset($formData['paciente_id']) && $formData['paciente_id'] == $paciente['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($paciente['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Lançamento
                    </button>
                    <a href="index.php?module=caixa&action=listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true
    });

    $('.money').mask('#.##0,00', {reverse: true});

    $('.select2').select2({
        placeholder: 'Selecione...',
        allowClear: true,
        width: '100%'
    });
});
</script>
