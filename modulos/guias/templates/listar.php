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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="index.php" method="get" class="form-inline">
                <input type="hidden" name="module" value="guias">
                <input type="hidden" name="action" value="list">
                
                <div class="form-group mx-sm-3 mb-2">
                    <label for="paciente" class="sr-only">Paciente</label>
                    <input type="text" class="form-control" id="paciente" name="paciente" placeholder="Nome ou documento"
                        value="<?php echo isset($_GET['paciente']) ? htmlspecialchars($_GET['paciente']) : ''; ?>">
                </div>
                
                <div class="form-group mx-sm-3 mb-2">
                    <label for="status" class="sr-only">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Todos os status</option>
                        <option value="agendado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'agendado') ? 'selected' : ''; ?>>Agendado</option>
                        <option value="realizado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'realizado') ? 'selected' : ''; ?>>Realizado</option>
                        <option value="cancelado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="form-group mx-sm-3 mb-2">
                    <label for="data_inicio" class="sr-only">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" placeholder="Data Início" 
                        value="<?php echo isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : ''; ?>">
                </div>
                
                <div class="form-group mx-sm-3 mb-2">
                    <label for="data_fim" class="sr-only">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" placeholder="Data Fim" 
                        value="<?php echo isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : ''; ?>">
                </div>
                
                <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                <a href="index.php?module=guias&action=list" class="btn btn-secondary mb-2 ml-2">Limpar</a>
            </form>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Guias Encontradas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Paciente</th>
                            <th>Procedimento</th>
                            <th>Especialidade</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($guias)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma guia encontrada</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($guias as $guia): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($guia['codigo']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($guia['paciente_nome']); ?>
                                        <?php if (!empty($guia['paciente_documento'])): ?>
                                            <br><small><?php echo htmlspecialchars($guia['paciente_documento']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($guia['procedimento_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($guia['especialidade_nome']); ?></td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($guia['data_agendamento'])); ?>
                                        <?php if (!empty($guia['horario_agendamento'])): ?>
                                            <br><small><?php echo $guia['horario_agendamento']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($guia['status'] == 'agendado'): ?>
                                            <span class="badge badge-primary">Agendado</span>
                                        <?php elseif ($guia['status'] == 'realizado'): ?>
                                            <span class="badge badge-success">Realizado</span>
                                        <?php elseif ($guia['status'] == 'cancelado'): ?>
                                            <span class="badge badge-danger">Cancelado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?module=guias&action=view&id=<?php echo $guia['id']; ?>" 
                                               class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($guia['status'] != 'cancelado'): ?>
                                                <a href="index.php?module=guias&action=edit&id=<?php echo $guia['id']; ?>" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?module=guias&action=print&id=<?php echo $guia['id']; ?>" 
                                               class="btn btn-secondary btn-sm" title="Imprimir" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
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
                                    <a class="page-link" href="index.php?module=guias&action=list&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['paciente']) ? '&paciente=' . urlencode($_GET['paciente']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . $_GET['data_inicio'] : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . $_GET['data_fim'] : ''; ?>">
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
                                    <a class="page-link" href="index.php?module=guias&action=list&page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['paciente']) ? '&paciente=' . urlencode($_GET['paciente']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . $_GET['data_inicio'] : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . $_GET['data_fim'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=guias&action=list&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['paciente']) ? '&paciente=' . urlencode($_GET['paciente']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . $_GET['data_inicio'] : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . $_GET['data_fim'] : ''; ?>">
                                        Próximo
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
            
            <div class="mt-3 text-muted">
                <small>Exibindo <?php echo count($guias); ?> de <?php echo $totalGuias; ?> guias.</small>
            </div>
        </div>
    </div>
</div>