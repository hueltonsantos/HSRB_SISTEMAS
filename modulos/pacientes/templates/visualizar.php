<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Paciente: <?php echo htmlspecialchars($paciente['nome']); ?></h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Informações do Paciente</h6>
            <div>
                <a href="index.php?module=pacientes&action=edit&id=<?php echo $paciente['id']; ?>" class="btn btn-warning btn-sm">
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
                                <p><?php echo $paciente['id']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Status:</p>
                                <p>
                                    <?php if ($paciente['status'] == 1): ?>
                                        <span class="badge badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inativo</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Dados Pessoais</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Nome Completo:</p>
                                <p><?php echo htmlspecialchars($paciente['nome']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Data de Nascimento:</p>
                                <p><?php echo isset($paciente['data_nascimento_formatada']) ? $paciente['data_nascimento_formatada'] : ''; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">CPF:</p>
                                <p><?php echo htmlspecialchars($paciente['cpf']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">RG:</p>
                                <p><?php echo htmlspecialchars($paciente['rg']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Sexo:</p>
                                <p>
                                    <?php 
                                    $sexoTexto = '';
                                    if ($paciente['sexo'] == 'M') {
                                        $sexoTexto = 'Masculino';
                                    } elseif ($paciente['sexo'] == 'F') {
                                        $sexoTexto = 'Feminino';
                                    } elseif ($paciente['sexo'] == 'O') {
                                        $sexoTexto = 'Outro';
                                    }
                                    echo $sexoTexto;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Endereço</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">CEP:</p>
                                <p><?php echo htmlspecialchars($paciente['cep']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Endereço:</p>
                                <p><?php echo htmlspecialchars($paciente['endereco']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Número:</p>
                                <p><?php echo htmlspecialchars($paciente['numero']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Complemento:</p>
                                <p><?php echo htmlspecialchars($paciente['complemento']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Bairro:</p>
                                <p><?php echo htmlspecialchars($paciente['bairro']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Cidade/UF:</p>
                                <p><?php echo htmlspecialchars($paciente['cidade']); ?>/<?php echo htmlspecialchars($paciente['estado']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Contato</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Telefone Fixo:</p>
                                <p><?php echo htmlspecialchars($paciente['telefone_fixo']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Celular:</p>
                                <p><?php echo htmlspecialchars($paciente['celular']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">E-mail:</p>
                                <p><?php echo htmlspecialchars($paciente['email']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Convênio</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Convênio:</p>
                                <p><?php echo htmlspecialchars($paciente['convenio']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Número da Carteirinha:</p>
                                <p><?php echo htmlspecialchars($paciente['numero_carteirinha']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Observações</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <p><?php echo nl2br(htmlspecialchars($paciente['observacoes'])); ?></p>
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
                                        <p class="text-secondary"><?php echo isset($paciente['data_cadastro_formatada']) ? $paciente['data_cadastro_formatada'] : ''; ?></p>
                                        
                                        <p class="mt-3 mb-1">Última Atualização:</p>
                                        <p class="text-secondary"><?php echo isset($paciente['ultima_atualizacao_formatada']) ? $paciente['ultima_atualizacao_formatada'] : ''; ?></p>
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
                                        <a href="index.php?module=agendamentos&action=new&paciente_id=<?php echo $paciente['id']; ?>" class="btn btn-primary btn-block">
                                            <i class="fas fa-calendar-plus"></i> Novo Agendamento
                                        </a>
                                        
                                        <a href="index.php?module=agendamentos&action=list&paciente_id=<?php echo $paciente['id']; ?>" class="btn btn-info btn-block mt-2">
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
                <p>Deseja realmente excluir o paciente <strong><?php echo htmlspecialchars($paciente['nome']); ?></strong>?</p>
                
                <form id="deleteForm" action="index.php?module=pacientes&action=delete" method="post">
                    <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
                    
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