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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Alterar Status da Guia</h6>
            <div>
                <a href="index.php?module=guias&action=view&id=<?php echo $guia['id']; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-12 col-md-6">
                    <h5>Informações da Guia</h5>
                    <p><strong>Código:</strong> <?php echo htmlspecialchars($guia['codigo']); ?></p>
                    <p><strong>Paciente:</strong> <?php echo htmlspecialchars($guia['paciente_nome']); ?></p>
                    <p><strong>Procedimento:</strong> <?php echo htmlspecialchars($guia['procedimento_nome']); ?></p>
                    <p><strong>Data Agendada:</strong> <?php echo date('d/m/Y', strtotime($guia['data_agendamento'])); ?>
                        <?php if (!empty($guia['horario_agendamento'])): ?>
                            às <?php echo $guia['horario_agendamento']; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-sm-12 col-md-6">
                    <h5>Status Atual</h5>
                    <?php if ($guia['status'] == 'agendado'): ?>
                        <div class="alert alert-primary">
                            <i class="fas fa-calendar-alt"></i> Agendado
                        </div>
                    <?php elseif ($guia['status'] == 'realizado'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> Realizado
                        </div>
                    <?php elseif ($guia['status'] == 'cancelado'): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-ban"></i> Cancelado
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <form action="index.php?module=guias&action=edit&id=<?php echo $guia['id']; ?>" method="post">
                <div class="form-group">
                    <label for="status">Novo Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="">Selecione um status</option>
                        <option value="agendado" <?php echo $guia['status'] == 'agendado' ? 'selected' : ''; ?>>Agendado</option>
                        <option value="realizado" <?php echo $guia['status'] == 'realizado' ? 'selected' : ''; ?>>Realizado</option>
                        <option value="cancelado" <?php echo $guia['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="observacoes">Observações / Instruções ao Paciente</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($guia['observacoes']); ?></textarea>
                </div>
                
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>