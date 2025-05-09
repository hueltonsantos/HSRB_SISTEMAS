<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Especialidade: <?php echo htmlspecialchars($especialidade['nome']); ?></h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Informações da Especialidade</h6>
            <div>
                <a href="index.php?module=especialidades&action=procedimentos&id=<?php echo $especialidade['id']; ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-list-alt"></i> Procedimentos
                </a>
                <a href="index.php?module=especialidades&action=edit&id=<?php echo $especialidade['id']; ?>" class="btn btn-warning btn-sm">
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
                                <p><?php echo $especialidade['id']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Status:</p>
                                <p>
                                    <?php if ($especialidade['status'] == 1): ?>
                                        <span class="badge badge-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inativa</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Dados da Especialidade</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Nome:</p>
                                <p><?php echo htmlspecialchars($especialidade['nome']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <p class="font-weight-bold mb-1">Descrição:</p>
                                <p>
                                    <?php if (!empty($especialidade['descricao'])): ?>
                                        <?php echo nl2br(htmlspecialchars($especialidade['descricao'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sem descrição</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Procedimentos e Valores</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box mb-4">
                                <?php if (empty($procedimentos)): ?>
                                    <p class="text-muted">Nenhum procedimento cadastrado para esta especialidade.</p>
                                    <a href="index.php?module=especialidades&action=add_procedimento&especialidade_id=<?php echo $especialidade['id']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-plus"></i> Adicionar Procedimento
                                    </a>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Procedimento</th>
                                                    <th>Valor</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($procedimentos as $procedimento): ?>
                                                    <tr>
                                                        <td><?php echo $procedimento['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($procedimento['procedimento']); ?></td>
                                                        <td><?php echo $procedimento['valor_formatado']; ?></td>
                                                        <td>
                                                            <?php if ($procedimento['status'] == 1): ?>
                                                                <span class="badge badge-success">Ativo</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Inativo</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="index.php?module=especialidades&action=procedimentos&id=<?php echo $especialidade['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-list-alt"></i> Gerenciar Procedimentos
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
                                        <p class="text-secondary"><?php echo isset($especialidade['data_cadastro_formatada']) ? $especialidade['data_cadastro_formatada'] : ''; ?></p>
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
                                        Estatísticas
                                    </div>
                                    <div class="mb-0 mt-3">
                                        <p class="font-weight-bold">Total de procedimentos:</p>
                                        <p class="h4 mb-3"><?php echo count($procedimentos); ?></p>
                                        
                                        <?php if (!empty($procedimentos)): ?>
                                            <?php
                                            $totalValores = 0;
                                            $maxValor = 0;
                                            $minValor = PHP_FLOAT_MAX;
                                            
                                            foreach ($procedimentos as $proc) {
                                                $totalValores += $proc['valor'];
                                                $maxValor = max($maxValor, $proc['valor']);
                                                $minValor = min($minValor, $proc['valor']);
                                            }
                                            
                                            $mediaValor = count($procedimentos) > 0 ? $totalValores / count($procedimentos) : 0;
                                            
                                            $valorProcedimentoModel = new ValorProcedimentoModel();
                                            ?>
                                            
                                            <p class="font-weight-bold mt-2">Valor médio:</p>
                                            <p class="text-success"><?php echo $valorProcedimentoModel->formatDecimalToCurrency($mediaValor); ?></p>
                                            
                                            <p class="font-weight-bold mt-2">Valor mínimo:</p>
                                            <p class="text-info"><?php echo $valorProcedimentoModel->formatDecimalToCurrency($minValor); ?></p>
                                            
                                            <p class="font-weight-bold mt-2">Valor máximo:</p>
                                            <p class="text-primary"><?php echo $valorProcedimentoModel->formatDecimalToCurrency($maxValor); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                <p>Deseja realmente excluir a especialidade <strong><?php echo htmlspecialchars($especialidade['nome']); ?></strong>?</p>
                
                <form id="deleteForm" action="index.php?module=especialidades&action=delete" method="post">
                    <input type="hidden" name="id" value="<?php echo $especialidade['id']; ?>">
                    
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
                
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <small>A exclusão física só será possível se não houver procedimentos ou clínicas vinculadas a esta especialidade.</small>
                </div>
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