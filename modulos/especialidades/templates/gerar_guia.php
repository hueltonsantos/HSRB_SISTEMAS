<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gerar Guia de Encaminhamento</h1>
    
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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Dados do Encaminhamento</h6>
            <div>
                <a href="index.php?module=especialidades&action=procedimentos&id=<?php echo $procedimento['especialidade_id']; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Procedimento</h5>
                    <p><strong><?php echo htmlspecialchars($procedimento['procedimento']); ?></strong></p>
                    <!-- Preço ocultado no encaminhamento -->
                    <p><strong>Especialidade:</strong> <?php echo htmlspecialchars($procedimento['especialidade_nome']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Clínica</h5>
                    <p><strong><?php echo htmlspecialchars($procedimento['clinica_nome'] ?? 'Não definida'); ?></strong></p>
                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($procedimento['endereco'] ?? 'Não definido'); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($procedimento['telefone'] ?? 'Não definido'); ?></p>
                </div>
            </div>
            
            <form action="index.php?module=especialidades&action=gerar_guia&procedimento_id=<?php echo $procedimentoId; ?>" method="post">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="paciente_id">Paciente *</label>
                            <div class="input-group">
                                <select class="form-control" id="paciente_id" name="paciente_id" required>
                                    <option value="">Selecione um paciente</option>
                                    <?php foreach ($pacientes as $paciente): ?>
                                    <option value="<?php echo $paciente['id']; ?>" 
                                            <?php echo (isset($_SESSION['form_data']['paciente_id']) && $_SESSION['form_data']['paciente_id'] == $paciente['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($paciente['nome']); ?> 
                                        <?php if (!empty($paciente['documento'])): ?>
                                            (<?php echo htmlspecialchars($paciente['documento']); ?>)
                                        <?php endif; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <a href="index.php?module=pacientes&action=new" class="btn btn-outline-secondary" target="_blank">
                                        <i class="fas fa-plus"></i> Novo Paciente
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data_agendamento">Data do Agendamento *</label>
                            <input type="date" class="form-control" id="data_agendamento" name="data_agendamento"
                                   value="<?php echo isset($_SESSION['form_data']['data_agendamento']) ? htmlspecialchars($_SESSION['form_data']['data_agendamento']) : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="horario_agendamento">Horário do Agendamento</label>
                            <input type="time" class="form-control" id="horario_agendamento" name="horario_agendamento"
                                   value="<?php echo isset($_SESSION['form_data']['horario_agendamento']) ? htmlspecialchars($_SESSION['form_data']['horario_agendamento']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="observacoes">Observações / Instruções ao Paciente</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4"><?php echo isset($_SESSION['form_data']['observacoes']) ? htmlspecialchars($_SESSION['form_data']['observacoes']) : ''; ?></textarea>
                </div>
                
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Gerar Guia</button>
                </div>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </div>
    </div>
</div>

<script>
// Se abrir a página em um popup através do botão "Novo Paciente", recarregar a página principal quando fechar
if (window.opener && !window.opener.closed) {
    window.addEventListener('unload', function() {
        if (!window.opener.closed) {
            window.opener.location.reload();
        }
    });
}
</script>