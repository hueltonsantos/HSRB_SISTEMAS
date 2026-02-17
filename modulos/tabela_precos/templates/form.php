<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $id ? 'Editar Preço' : 'Novo Preço'; ?></h1>
        <a href="index.php?module=tabela_precos" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Voltar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados do Procedimento</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=tabela_precos&action=save" method="POST">
                <input type="hidden" name="id" value="<?php echo $preco['id']; ?>">
                
                <div class="form-group">
                    <label for="procedimento">Nome do Procedimento</label>
                    <input type="text" class="form-control" id="procedimento" name="procedimento" 
                           value="<?php echo htmlspecialchars($preco['procedimento']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="especialidade_id">Especialidade</label>
                    <select class="form-control" id="especialidade_id" name="especialidade_id" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($especialidades as $e): ?>
                            <option value="<?php echo $e['id']; ?>" 
                                <?php echo ($preco['especialidade_id'] == $e['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="valor_paciente">Valor Paciente (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="valor_paciente" name="valor_paciente" 
                               value="<?php echo $preco['valor_paciente']; ?>" required>
                        <small class="form-text text-muted">Valor cobrado do paciente.</small>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="valor_repasse">Valor Repasse (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="valor_repasse" name="valor_repasse" 
                               value="<?php echo $preco['valor_repasse']; ?>" required>
                        <small class="form-text text-muted">Custo ou repasse ao médico/clínica.</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                               <?php echo $preco['status'] ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="status">Ativo</label>
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <?php if ($id): ?>
                    <button type="button" class="btn btn-danger ml-2" onclick="if(confirm('Excluir este preço?')) window.location.href='index.php?module=tabela_precos&action=delete&id=<?php echo $id; ?>'">Excluir</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
