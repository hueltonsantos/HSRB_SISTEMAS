<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detalhes do Agendamento</h1>
    
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informações do Agendamento</h6>
                    <div>
                        <a href="index.php?module=agendamentos&action=edit&id=<?php echo $agendamento['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">ID:</p>
                                <p><?php echo $agendamento['id']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Status:</p>
                                <p>
                                    <span class="badge badge-<?php echo $statusAtual['cor']; ?> p-2">
                                        <i class="fas fa-<?php echo $statusAtual['icone']; ?> mr-1"></i>
                                        <?php echo $statusAtual['texto']; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Data da Consulta:</p>
                                <p><?php echo $agendamento['data_consulta_formatada']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Hora da Consulta:</p>
                                <p><?php echo substr($agendamento['hora_consulta'], 0, 5); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Dados do Paciente</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Paciente:</p>
                                <p>
                                    <?php echo htmlspecialchars($agendamento['paciente_nome']); ?>
                                    <a href="index.php?module=pacientes&action=view&id=<?php echo $agendamento['paciente_id']; ?>" class="btn btn-info btn-sm ml-2">
                                        <i class="fas fa-user"></i> Ver Perfil
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Telefone:</p>
                                <p>
                                    <?php echo !empty($agendamento['paciente_celular']) ? htmlspecialchars($agendamento['paciente_celular']) : 'Não informado'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Clínica e Especialidade</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Clínica:</p>
                                <p>
                                    <?php echo htmlspecialchars($agendamento['clinica_nome']); ?>
                                    <a href="index.php?module=clinicas&action=view&id=<?php echo $agendamento['clinica_id']; ?>" class="btn btn-info btn-sm ml-2">
                                        <i class="fas fa-hospital"></i> Ver Clínica
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Especialidade:</p>
                                <p>
                                    <?php echo htmlspecialchars($agendamento['especialidade_nome']); ?>
                                    <a href="index.php?module=especialidades&action=view&id=<?php echo $agendamento['especialidade_id']; ?>" class="btn btn-info btn-sm ml-2">
                                        <i class="fas fa-stethoscope"></i> Ver Especialidade
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Endereço da Clínica:</p>
                                <p><?php echo !empty($agendamento['clinica_endereco']) ? htmlspecialchars($agendamento['clinica_endereco']) : 'Não informado'; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Telefone da Clínica:</p>
                                <p><?php echo !empty($agendamento['clinica_telefone']) ? htmlspecialchars($agendamento['clinica_telefone']) : 'Não informado'; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Observações</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <?php if (!empty($agendamento['observacoes'])): ?>
                                    <p><?php echo nl2br(htmlspecialchars($agendamento['observacoes'])); ?></p>
                                <?php else: ?>
                                    <p class="text-muted">Nenhuma observação registrada</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Card de Status -->
            <div class="card border-left-<?php echo $statusAtual['cor']; ?> shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-<?php echo $statusAtual['cor']; ?>">Status do Agendamento</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="display-4 text-<?php echo $statusAtual['cor']; ?>">
                            <i class="fas fa-<?php echo $statusAtual['icone']; ?>"></i>
                        </span>
                        <h4 class="mt-3 text-<?php echo $statusAtual['cor']; ?>"><?php echo $statusAtual['texto']; ?></h4>
                    </div>
                    
                    <hr>
                    
                    <p class="text-center">Alterar status para:</p>
                    
                    <div class="mb-3 d-flex flex-column">
                        <?php if ($agendamento['status_agendamento'] != 'agendado'): ?>
                            <form action="index.php?module=agendamentos&action=update_status" method="post" class="mb-2">
                                <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                <input type="hidden" name="status" value="agendado">
                                <button type="submit" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-calendar-check"></i> Agendado
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($agendamento['status_agendamento'] != 'confirmado'): ?>
                            <form action="index.php?module=agendamentos&action=update_status" method="post" class="mb-2">
                                <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                <input type="hidden" name="status" value="confirmado">
                                <button type="submit" class="btn btn-outline-info btn-block">
                                    <i class="fas fa-calendar-check"></i> Confirmado
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($agendamento['status_agendamento'] != 'realizado'): ?>
                            <form action="index.php?module=agendamentos&action=update_status" method="post" class="mb-2">
                                <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                <input type="hidden" name="status" value="realizado">
                                <button type="submit" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-check-circle"></i> Realizado
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($agendamento['status_agendamento'] != 'cancelado'): ?>
                            <form action="index.php?module=agendamentos&action=update_status" method="post" class="mb-2">
                                <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                <input type="hidden" name="status" value="cancelado">
                                <button type="submit" class="btn btn-outline-danger btn-block">
                                    <i class="fas fa-calendar-times"></i> Cancelado
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Card de Informações de Cadastro -->
            <div class="card border-left-info shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Informações de Cadastro</h6>
                </div>
                <div class="card-body">
                    <div class="mb-0 font-weight-bold text-gray-800">
                        <p class="mt-2 mb-1">Data de Agendamento:</p>
                        <p class="text-secondary"><?php echo isset($agendamento['data_agendamento_formatada']) ? $agendamento['data_agendamento_formatada'] : 'Não informado'; ?></p>
                        
                        <p class="mt-3 mb-1">Última Atualização:</p>
                        <p class="text-secondary"><?php echo isset($agendamento['ultima_atualizacao_formatada']) ? $agendamento['ultima_atualizacao_formatada'] : 'Não informado'; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Card de Ações Rápidas -->
            <div class="card border-left-warning shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Ações Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-0">
                        <a href="index.php?module=agendamentos&action=new&paciente_id=<?php echo $agendamento['paciente_id']; ?>" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-calendar-plus"></i> Novo Agendamento para este Paciente
                        </a>
                        
                        <a href="index.php?module=agendamentos&action=list&paciente_id=<?php echo $agendamento['paciente_id']; ?>" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-calendar"></i> Ver Todos os Agendamentos do Paciente
                        </a>
                        
                        <a href="tel:<?php echo $agendamento['paciente_celular']; ?>" class="btn btn-success btn-block">
                            <i class="fas fa-phone"></i> Ligar para o Paciente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deseja realmente excluir este agendamento?</p>
                
                <form id="deleteForm" action="index.php?module=agendamentos&action=delete" method="post">
                    <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                    
                    <div class="form-group">
                        <label>Ação:</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="cancelar" name="tipo_exclusao" class="custom-control-input" value="cancelar" checked>
                            <label class="custom-control-label" for="cancelar">
                                Cancelar agendamento (recomendado)
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="excluir" name="tipo_exclusao" class="custom-control-input" value="excluir">
                            <label class="custom-control-label" for="excluir">
                                Excluir permanentemente
                            </label>
                        </div>
                    </div>
                </form>
                
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <small>Quando possível, prefira cancelar o agendamento em vez de excluí-lo para manter o histórico.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteForm').submit();">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.info-box {
    border-bottom: 1px solid #f7f7f7;
}
</style>