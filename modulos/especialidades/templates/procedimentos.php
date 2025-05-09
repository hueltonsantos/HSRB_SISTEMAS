<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Procedimentos da Especialidade: <?php echo htmlspecialchars($especialidade['nome']); ?></h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Gerenciar Procedimentos</h6>
            <div>
                <a href="index.php?module=especialidades&action=view&id=<?php echo $especialidade['id']; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar para Detalhes da Especialidade
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <a href="index.php?module=especialidades&action=add_procedimento&especialidade_id=<?php echo $especialidade['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Procedimento
                        </a>
                        <a href="index.php?module=especialidades&action=batch_procedimentos&especialidade_id=<?php echo $especialidade['id']; ?>" class="btn btn-success ml-2">
                            <i class="fas fa-list-check"></i> Adicionar em Lote
                        </a>
                    </div>
                    
                    <div class="col-md-6 text-right">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Filtrar procedimentos..." id="searchInput">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="procedimentosTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Procedimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($procedimentos)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum procedimento cadastrado para esta especialidade.</td>
                            </tr>
                        <?php else: ?>
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
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?module=especialidades&action=edit_procedimento&id=<?php echo $procedimento['id']; ?>" 
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                data-toggle="modal" data-target="#deleteModal<?php echo $procedimento['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de Confirmação de Exclusão -->
                                        <div class="modal fade" id="deleteModal<?php echo $procedimento['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Exclusão</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Deseja realmente excluir o procedimento <strong><?php echo htmlspecialchars($procedimento['procedimento']); ?></strong>?</p>
                                                        
                                                        <form id="deleteForm<?php echo $procedimento['id']; ?>" action="index.php?module=especialidades&action=delete_procedimento" method="post">
                                                            <input type="hidden" name="id" value="<?php echo $procedimento['id']; ?>">
                                                            <input type="hidden" name="especialidade_id" value="<?php echo $especialidade['id']; ?>">
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="button" class="btn btn-danger" 
                                                            onclick="document.getElementById('deleteForm<?php echo $procedimento['id']; ?>').submit();">
                                                            Confirmar Exclusão
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Filtro de busca na tabela
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#procedimentosTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>