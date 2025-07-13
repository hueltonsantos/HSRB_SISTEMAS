<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Especialidades</h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Listagem de Especialidades</h6>
            <div>
                <a href="index.php?module=especialidades&action=new" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nova Especialidade
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros de busca -->
            <div class="mb-4">
                <form action="index.php" method="get" class="form-inline">
                    <input type="hidden" name="module" value="especialidades">
                    <input type="hidden" name="action" value="list">
                    
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="nome" class="sr-only">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da Especialidade" 
                            value="<?php echo isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : ''; ?>">
                    </div>
                    
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="status" class="sr-only">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1" <?php echo (!isset($_GET['status']) || $_GET['status'] == 1) ? 'selected' : ''; ?>>Ativas</option>
                            <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : ''; ?>>Inativas</option>
                            <option value="" <?php echo (isset($_GET['status']) && $_GET['status'] === '') ? 'selected' : ''; ?>>Todas</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                    <a href="index.php?module=especialidades&action=list" class="btn btn-secondary mb-2 ml-2">Limpar</a>
                </form>
            </div>
            
            <!-- Tabela de Especialidades -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($especialidades)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma especialidade encontrada</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($especialidades as $especialidade): ?>
                                <tr>
                                    <td><?php echo $especialidade['id']; ?></td>
                                    <td><?php echo htmlspecialchars($especialidade['nome']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($especialidade['descricao'])) {
                                            echo nl2br(htmlspecialchars(substr($especialidade['descricao'], 0, 100)));
                                            if (strlen($especialidade['descricao']) > 100) {
                                                echo '...';
                                            }
                                        } else {
                                            echo '<span class="text-muted">Sem descrição</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($especialidade['status'] == 1): ?>
                                            <span class="badge badge-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inativa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?module=especialidades&action=view&id=<?php echo $especialidade['id']; ?>" 
                                                class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="index.php?module=especialidades&action=edit&id=<?php echo $especialidade['id']; ?>" 
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="index.php?module=especialidades&action=procedimentos&id=<?php echo $especialidade['id']; ?>" 
                                                class="btn btn-success btn-sm" title="Procedimentos">
                                                <i class="fas fa-list-alt"></i>
                                            </a>
                                            
                                            <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                data-toggle="modal" data-target="#deleteModal<?php echo $especialidade['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de Confirmação de Exclusão -->
                                        <div class="modal fade" id="deleteModal<?php echo $especialidade['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                        
                                                        <form id="deleteForm<?php echo $especialidade['id']; ?>" action="index.php?module=especialidades&action=delete" method="post">
                                                            <input type="hidden" name="id" value="<?php echo $especialidade['id']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label>Tipo de Exclusão:</label>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" id="logica<?php echo $especialidade['id']; ?>" name="tipo_exclusao" 
                                                                        class="custom-control-input" value="logica" checked>
                                                                    <label class="custom-control-label" for="logica<?php echo $especialidade['id']; ?>">
                                                                        Desativar (exclusão lógica)
                                                                    </label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" id="fisica<?php echo $especialidade['id']; ?>" name="tipo_exclusao" 
                                                                        class="custom-control-input" value="fisica">
                                                                    <label class="custom-control-label" for="fisica<?php echo $especialidade['id']; ?>">
                                                                        Excluir permanentemente (exclusão física)
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="button" class="btn btn-danger" 
                                                            onclick="document.getElementById('deleteForm<?php echo $especialidade['id']; ?>').submit();">
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
            
            <!-- Paginação -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Navegação de páginas">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=especialidades&action=list&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['nome']) ? '&nome=' . urlencode($_GET['nome']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                                        Anterior
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            // Determina quais páginas exibir
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                $startPage = max(1, $endPage - 4);
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?module=especialidades&action=list&page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['nome']) ? '&nome=' . urlencode($_GET['nome']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=especialidades&action=list&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['nome']) ? '&nome=' . urlencode($_GET['nome']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?>">
                                        Próximo
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
            
            <div class="mt-3 text-muted">
                <small>Exibindo <?php echo count($especialidades); ?> de <?php echo $totalEspecialidades; ?> especialidades.</small>
            </div>
        </div>
    </div>
</div>