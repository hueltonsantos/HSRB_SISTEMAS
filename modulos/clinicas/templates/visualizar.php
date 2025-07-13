<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Clínica: <?php echo htmlspecialchars($clinica['nome']); ?></h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Informações da Clínica</h6>
            <div>
                <a href="index.php?module=clinicas&action=especialidades&id=<?php echo $clinica['id']; ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-stethoscope"></i> Especialidades
                </a>
                <a href="index.php?module=clinicas&action=edit&id=<?php echo $clinica['id']; ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">ID:</p>
                                <p><?php echo $clinica['id']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Status:</p>
                                <p>
                                    <?php if ($clinica['status'] == 1): ?>
                                        <span class="badge badge-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inativa</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Dados da Clínica</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Nome/Nome Fantasia:</p>
                                <p><?php echo htmlspecialchars($clinica['nome']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Razão Social:</p>
                                <p><?php echo htmlspecialchars($clinica['razao_social']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">CNPJ:</p>
                                <p><?php echo htmlspecialchars($clinica['cnpj']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Responsável:</p>
                                <p><?php echo htmlspecialchars($clinica['responsavel']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Endereço</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">CEP:</p>
                                <p><?php echo htmlspecialchars($clinica['cep']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Endereço:</p>
                                <p><?php echo htmlspecialchars($clinica['endereco']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Número:</p>
                                <p><?php echo htmlspecialchars($clinica['numero']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Complemento:</p>
                                <p><?php echo htmlspecialchars($clinica['complemento']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Bairro:</p>
                                <p><?php echo htmlspecialchars($clinica['bairro']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Cidade/UF:</p>
                                <p><?php echo htmlspecialchars($clinica['cidade']); ?>/<?php echo htmlspecialchars($clinica['estado']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Contato</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Telefone:</p>
                                <p><?php echo htmlspecialchars($clinica['telefone']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Celular:</p>
                                <p><?php echo htmlspecialchars($clinica['celular']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">E-mail:</p>
                                <p><?php echo htmlspecialchars($clinica['email']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Site:</p>
                                <p>
                                    <?php if (!empty($clinica['site'])): ?>
                                        <a href="<?php echo htmlspecialchars($clinica['site']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($clinica['site']); ?>
                                            <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Não informado</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Especialidades Disponíveis</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <?php if (empty($especialidades)): ?>
                                    <p class="text-muted">Nenhuma especialidade cadastrada para esta clínica.</p>
                                    <a href="index.php?module=clinicas&action=especialidades&id=<?php echo $clinica['id']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-plus"></i> Adicionar Especialidades
                                    </a>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($especialidades as $especialidade): ?>
                                            <div class="col-md-4 mb-2">
                                                <span class="badge badge-info p-2">
                                                    <i class="fas fa-stethoscope mr-1"></i>
                                                    <?php echo htmlspecialchars($especialidade['nome']); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="mt-3">
                                        <a href="index.php?module=clinicas&action=especialidades&id=<?php echo $clinica['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> Gerenciar Especialidades
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Informações de Cadastro
                                    </div>
                                    <div class="mb-0 font-weight-bold text-gray-800">
                                        <p class="mt-3 mb-1">Data de Cadastro:</p>
                                        <p class="text-secondary"><?php echo isset($clinica['data_cadastro_formatada']) ? $clinica['data_cadastro_formatada'] : ''; ?></p>
                                        
                                        <p class="mt-3 mb-1">Última Atualização:</p>
                                        <p class="text-secondary"><?php echo isset($clinica['ultima_atualizacao_formatada']) ? $clinica['ultima_atualizacao_formatada'] : ''; ?></p>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-left-warning shadow h-100 py-2 mt-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Ações Disponíveis
                                    </div>
                                    <div class="mb-0 mt-3">
                                        <a href="index.php?module=agendamentos&action=new&clinica_id=<?php echo $clinica['id']; ?>" class="btn btn-primary btn-block">
                                            <i class="fas fa-calendar-plus"></i> Novo Agendamento
                                        </a>
                                        
                                        <a href="index.php?module=agendamentos&action=list&clinica_id=<?php echo $clinica['id']; ?>" class="btn btn-info btn-block mt-2">
                                            <i class="fas fa-calendar"></i> Ver Agendamentos
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
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
                <p>Deseja realmente excluir a clínica <strong><?php echo htmlspecialchars($clinica['nome']); ?></strong>?</p>
                
                <form id="deleteForm" action="index.php?module=clinicas&action=delete" method="post">
                    <input type="hidden" name="id" value="<?php echo $clinica['id']; ?>">
                    
                    <div class="form-group">
                        <label>Tipo de Exclusão:</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="logica" name="tipo_exclusao" class="custom-control-input" value="logica" checked>
                            <label class="custom-control-label" for="logica">
                                Desativar (exclusão lógica)
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="fisica" name="tipo_exclusao" class="custom-control-input" value="fisica">
                            <label class="custom-control-label" for="fisica">
                                Excluir permanentemente (exclusão física)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteForm').submit();">
                    Confirmar Exclusão
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