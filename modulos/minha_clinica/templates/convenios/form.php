<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-medkit mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=convenios" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Voltar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados do Convênio</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=minha_clinica&action=salvar_convenio" method="POST">
                <?php if (isset($convenio['id'])): ?>
                    <input type="hidden" name="id" value="<?= $convenio['id'] ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nome_fantasia">Nome Fantasia <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia" 
                               value="<?= htmlspecialchars($convenio['nome_fantasia'] ?? '') ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="razao_social">Razão Social</label>
                        <input type="text" class="form-control" id="razao_social" name="razao_social" 
                               value="<?= htmlspecialchars($convenio['razao_social'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" class="form-control" id="cnpj" name="cnpj" 
                               value="<?= htmlspecialchars($convenio['cnpj'] ?? '') ?>">
                        <small class="form-text text-muted">Apenas números</small>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="registro_ans">Registro ANS</label>
                        <input type="text" class="form-control" id="registro_ans" name="registro_ans" 
                               value="<?= htmlspecialchars($convenio['registro_ans'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dias_retorno">Dias Retorno</label>
                        <input type="number" class="form-control" id="dias_retorno" name="dias_retorno" 
                               value="<?= $convenio['dias_retorno'] ?? 30 ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="prazo_recebimento_dias">Prazo Pagto (Dias)</label>
                        <input type="number" class="form-control" id="prazo_recebimento_dias" name="prazo_recebimento_dias" 
                               value="<?= $convenio['prazo_recebimento_dias'] ?? 30 ?>">
                        <small class="form-text text-muted">Para previsão de caixa</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" 
                               <?= (!isset($convenio['ativo']) || $convenio['ativo']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="ativo">Convênio Ativo</label>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="index.php?module=minha_clinica&action=convenios" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
