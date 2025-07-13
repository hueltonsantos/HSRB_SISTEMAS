<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Agendamentos</h1>
    
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
            <h6 class="m-0 font-weight-bold text-primary">Listagem de Agendamentos</h6>
            <div>
                <a href="index.php?module=agendamentos&action=calendar" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-calendar-alt"></i> Calendário
                </a>
                <a href="index.php?module=agendamentos&action=new" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Novo Agendamento
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros de busca -->
            <div class="mb-4">
                <form action="index.php" method="get" class="form">
                    <input type="hidden" name="module" value="agendamentos">
                    <input type="hidden" name="action" value="list">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="paciente_nome">Paciente</label>
                            <input type="text" class="form-control" id="paciente_nome" name="paciente_nome" 
                                value="<?php echo isset($_GET['paciente_nome']) ? htmlspecialchars($_GET['paciente_nome']) : ''; ?>"
                                placeholder="Nome do paciente">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="clinica_id">Clínica</label>
                            <select class="form-control" id="clinica_id" name="clinica_id">
                                <option value="">Todas</option>
                                <?php foreach ($clinicas as $clinica): ?>
                                    <option value="<?php echo $clinica['id']; ?>" <?php echo (isset($_GET['clinica_id']) && $_GET['clinica_id'] == $clinica['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($clinica['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="especialidade_id">Especialidade</label>
                            <select class="form-control" id="especialidade_id" name="especialidade_id">
                                <option value="">Todas</option>
                                <?php foreach ($especialidades as $especialidade): ?>
                                    <option value="<?php echo $especialidade['id']; ?>" <?php echo (isset($_GET['especialidade_id']) && $_GET['especialidade_id'] == $especialidade['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($especialidade['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="data_inicio">Data Início</label>
                            <input type="text" class="form-control datepicker" id="data_inicio" name="data_inicio" 
                                value="<?php echo isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : ''; ?>"
                                placeholder="DD/MM/AAAA">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="data_fim">Data Fim</label>
                            <input type="text" class="form-control datepicker" id="data_fim" name="data_fim" 
                                value="<?php echo isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : ''; ?>"
                                placeholder="DD/MM/AAAA">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status_agendamento">Status</label>
                            <select class="form-control" id="status_agendamento" name="status_agendamento">
                                <option value="">Todos</option>
                                <?php foreach ($statusAgendamento as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($_GET['status_agendamento']) && $_GET['status_agendamento'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mb-2 mr-2">Filtrar</button>
                            <a href="index.php?module=agendamentos&action=list" class="btn btn-secondary mb-2">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Tabela de Agendamentos -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Clínica</th>
                            <th>Especialidade</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agendamentos)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum agendamento encontrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($agendamentos as $agendamento): ?>
                                <tr>
                                    <td><?php echo $agendamento['id']; ?></td>
                                    <td><?php echo htmlspecialchars($agendamento['paciente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($agendamento['clinica_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($agendamento['especialidade_nome']); ?></td>
                                    <td><?php echo $agendamento['data_consulta_formatada']; ?></td>
                                    <td><?php echo substr($agendamento['hora_consulta'], 0, 5); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        switch($agendamento['status_agendamento']) {
                                            case 'agendado':
                                                $statusClass = 'primary';
                                                break;
                                            case 'confirmado':
                                                $statusClass = 'info';
                                                break;
                                            case 'realizado':
                                                $statusClass = 'success';
                                                break;
                                            case 'cancelado':
                                                $statusClass = 'danger';
                                                break;
                                            default:
                                                $statusClass = 'secondary';
                                        }
                                        ?>
                                        <span class="badge badge-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($agendamento['status_agendamento']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?module=agendamentos&action=view&id=<?php echo $agendamento['id']; ?>" 
                                                class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="index.php?module=agendamentos&action=edit&id=<?php echo $agendamento['id']; ?>" 
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                data-toggle="modal" data-target="#deleteModal<?php echo $agendamento['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Dropdown de alteração rápida de status -->
                                        <div class="btn-group mt-1" role="group">
                                            <button id="statusDropdown<?php echo $agendamento['id']; ?>" type="button" 
                                                class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Alterar Status
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="statusDropdown<?php echo $agendamento['id']; ?>">
                                                <?php if ($agendamento['status_agendamento'] != 'agendado'): ?>
                                                    <form action="index.php?module=agendamentos&action=update_status" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                                        <input type="hidden" name="status" value="agendado">
                                                        <button type="submit" class="dropdown-item">Marcar como Agendado</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($agendamento['status_agendamento'] != 'confirmado'): ?>
                                                    <form action="index.php?module=agendamentos&action=update_status" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                                        <input type="hidden" name="status" value="confirmado">
                                                        <button type="submit" class="dropdown-item">Marcar como Confirmado</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($agendamento['status_agendamento'] != 'realizado'): ?>
                                                    <form action="index.php?module=agendamentos&action=update_status" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                                        <input type="hidden" name="status" value="realizado">
                                                        <button type="submit" class="dropdown-item">Marcar como Realizado</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($agendamento['status_agendamento'] != 'cancelado'): ?>
                                                    <form action="index.php?module=agendamentos&action=update_status" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                                        <input type="hidden" name="status" value="cancelado">
                                                        <button type="submit" class="dropdown-item">Marcar como Cancelado</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Modal de Confirmação de Exclusão -->
                                        <div class="modal fade" id="deleteModal<?php echo $agendamento['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Exclusão</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Deseja realmente excluir o agendamento do paciente <strong><?php echo htmlspecialchars($agendamento['paciente_nome']); ?></strong>?</p>
                                                        
                                                        <form id="deleteForm<?php echo $agendamento['id']; ?>" action="index.php?module=agendamentos&action=delete" method="post">
                                                            <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label>Ação:</label>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" id="cancelar<?php echo $agendamento['id']; ?>" name="tipo_exclusao" 
                                                                        class="custom-control-input" value="cancelar" checked>
                                                                    <label class="custom-control-label" for="cancelar<?php echo $agendamento['id']; ?>">
                                                                        Cancelar agendamento (recomendado)
                                                                    </label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" id="excluir<?php echo $agendamento['id']; ?>" name="tipo_exclusao" 
                                                                        class="custom-control-input" value="excluir">
                                                                    <label class="custom-control-label" for="excluir<?php echo $agendamento['id']; ?>">
                                                                        Excluir permanentemente
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="button" class="btn btn-danger" 
                                                            onclick="document.getElementById('deleteForm<?php echo $agendamento['id']; ?>').submit();">
                                                            Confirmar
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
                                    <a class="page-link" href="index.php?module=agendamentos&action=list&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['paciente_nome']) ? '&paciente_nome=' . urlencode($_GET['paciente_nome']) : ''; ?><?php echo isset($_GET['clinica_id']) ? '&clinica_id=' . $_GET['clinica_id'] : ''; ?><?php echo isset($_GET['especialidade_id']) ? '&especialidade_id=' . $_GET['especialidade_id'] : ''; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . urlencode($_GET['data_inicio']) : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . urlencode($_GET['data_fim']) : ''; ?><?php echo isset($_GET['status_agendamento']) ? '&status_agendamento=' . $_GET['status_agendamento'] : ''; ?>">
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
                                    <a class="page-link" href="index.php?module=agendamentos&action=list&page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['paciente_nome']) ? '&paciente_nome=' . urlencode($_GET['paciente_nome']) : ''; ?><?php echo isset($_GET['clinica_id']) ? '&clinica_id=' . $_GET['clinica_id'] : ''; ?><?php echo isset($_GET['especialidade_id']) ? '&especialidade_id=' . $_GET['especialidade_id'] : ''; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . urlencode($_GET['data_inicio']) : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . urlencode($_GET['data_fim']) : ''; ?><?php echo isset($_GET['status_agendamento']) ? '&status_agendamento=' . $_GET['status_agendamento'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?module=agendamentos&action=list&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?><?php echo isset($_GET['paciente_nome']) ? '&paciente_nome=' . urlencode($_GET['paciente_nome']) : ''; ?><?php echo isset($_GET['clinica_id']) ? '&clinica_id=' . $_GET['clinica_id'] : ''; ?><?php echo isset($_GET['especialidade_id']) ? '&especialidade_id=' . $_GET['especialidade_id'] : ''; ?><?php echo isset($_GET['data_inicio']) ? '&data_inicio=' . urlencode($_GET['data_inicio']) : ''; ?><?php echo isset($_GET['data_fim']) ? '&data_fim=' . urlencode($_GET['data_fim']) : ''; ?><?php echo isset($_GET['status_agendamento']) ? '&status_agendamento=' . $_GET['status_agendamento'] : ''; ?>">
                                        Próximo
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
            
            <div class="mt-3 text-muted">
                <small>Exibindo <?php echo count($agendamentos); ?> de <?php echo $totalAgendamentos; ?> agendamentos.</small>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Inicializa os datepickers
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true
    });
});
</script>