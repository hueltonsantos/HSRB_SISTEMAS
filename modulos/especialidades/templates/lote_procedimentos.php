<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Adicionar Procedimentos em Lote</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Especialidade: <?php echo htmlspecialchars($especialidade['nome']); ?></h6>
            <div>
                <a href="index.php?module=especialidades&action=procedimentos&id=<?php echo $especialidadeId; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar para Procedimentos
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="index.php?module=especialidades&action=save_batch_procedimentos" method="post" id="batchForm">
                <input type="hidden" name="especialidade_id" value="<?php echo $especialidadeId; ?>">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Adicione vários procedimentos de uma só vez. Cada linha representa um procedimento.
                    <br>Formato: <strong>Nome do Procedimento | Valor</strong> (ex: Consulta | 150.00)
                </div>
                
                <div class="form-group">
                    <label for="procedimentos">Procedimentos (um por linha)</label>
                    <textarea class="form-control" id="procedimentos" name="procedimentos" rows="15" 
                              placeholder="Nome do Procedimento | Valor"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Procedimentos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('batchForm').addEventListener('submit', function(e) {
    var procedimentos = document.getElementById('procedimentos').value.trim();
    if (!procedimentos) {
        e.preventDefault();
        alert('Por favor, adicione pelo menos um procedimento.');
        return false;
    }
    
    // Validação básica do formato
    var lines = procedimentos.split('\n');
    var hasError = false;
    var errorLines = [];
    
    for (var i = 0; i < lines.length; i++) {
        var line = lines[i].trim();
        if (line && !line.includes('|')) {
            hasError = true;
            errorLines.push(i + 1);
        }
    }
    
    if (hasError) {
        e.preventDefault();
        alert('Formato inválido nas linhas: ' + errorLines.join(', ') + '. Use o formato "Nome | Valor".');
        return false;
    }
});
</script>