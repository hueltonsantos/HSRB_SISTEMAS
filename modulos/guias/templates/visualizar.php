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
            <h6 class="m-0 font-weight-bold text-primary">Detalhes da Guia</h6>
            <div>
                <a href="index.php?module=guias&action=list" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="index.php?module=guias&action=print&id=<?php echo $guia['id']; ?>" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-print"></i> Imprimir
                </a>
                
                <?php if ($guia['status'] != 'cancelado'): ?>
                    <a href="index.php?module=guias&action=edit&id=<?php echo $guia['id']; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar Status
                    </a>
                    
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cancelarModal">
                        <i class="fas fa-ban"></i> Cancelar
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Informações da Guia</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Código:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['codigo']); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Data de Emissão:</div>
                                <div class="col-md-8"><?php echo date('d/m/Y H:i', strtotime($guia['data_emissao'])); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Status:</div>
                                <div class="col-md-8">
                                    <?php if ($guia['status'] == 'agendado'): ?>
                                        <span class="badge badge-primary">Agendado</span>
                                    <?php elseif ($guia['status'] == 'realizado'): ?>
                                        <span class="badge badge-success">Realizado</span>
                                    <?php elseif ($guia['status'] == 'cancelado'): ?>
                                        <span class="badge badge-danger">Cancelado</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Data Agendada:</div>
                                <div class="col-md-8">
                                    <?php echo date('d/m/Y', strtotime($guia['data_agendamento'])); ?>
                                    <?php if (!empty($guia['horario_agendamento'])): ?>
                                        às <?php echo $guia['horario_agendamento']; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Procedimento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Nome:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['procedimento_nome']); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Valor:</div>
                                <div class="col-md-8">R$ <?php echo number_format($guia['procedimento_valor'], 2, ',', '.'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Especialidade:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['especialidade_nome']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Paciente</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Nome:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['paciente_nome']); ?></div>
                            </div>
                            <?php if (!empty($guia['paciente_documento'])): ?>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Documento:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['paciente_documento']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Clínica</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Nome:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['clinica_nome'] ?? 'Não definida'); ?></div>
                            </div>
                            <?php if (!empty($guia['endereco'])): ?>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Endereço:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['endereco']); ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($guia['telefone'])): ?>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Telefone:</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($guia['telefone']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($guia['observacoes'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Observações / Instruções</h6>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($guia['observacoes'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Cancelamento -->
<?php if ($guia['status'] != 'cancelado'): ?>
<div class="modal fade" id="cancelarModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Cancelamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar a guia <strong><?php echo $guia['codigo']; ?></strong>?</p>
                <p>Esta ação não poderá ser desfeita.</p>
                
                <form id="cancelarForm" action="index.php?module=guias&action=cancel" method="post">
                    <input type="hidden" name="id" value="<?php echo $guia['id']; ?>">
                    
                    <div class="form-group">
                        <label for="motivo">Motivo do Cancelamento:</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('cancelarForm').submit();">
                    Confirmar Cancelamento
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>