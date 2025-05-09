<?php
// Verifica se o acesso é direto
if (!defined('BASEPATH')) exit('Acesso negado');

// Inicializa o controlador
$controller = new ProcedimentoController($db);
$procedimentos = $controller->listar();

// Gerencia mensagens
$mensagem = isset($_SESSION['mensagem']) ? $_SESSION['mensagem'] : null;
unset($_SESSION['mensagem']);
?>

<div class="container-fluid">
    <h1 class="mt-4">Procedimentos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Procedimentos</li>
    </ol>
    
    <?php if (isset($mensagem['sucesso'])): ?>
        <div class="alert alert-success"><?= $mensagem['sucesso'] ?></div>
    <?php endif; ?>
    
    <?php if (isset($mensagem['erro'])): ?>
        <div class="alert alert-danger"><?= $mensagem['erro'] ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <i class="fas fa-table mr-1"></i> Lista de Procedimentos
            <a href="index.php?page=procedimentos/cadastrar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Procedimento
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Procedimento</th>
                            <th>Especialidade</th>
                            <th>Valor Padrão</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $procedimentos->fetch_assoc()): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= $p['procedimento'] ?></td>
                                <td><?= $p['especialidade'] ?></td>
                                <td>R$ <?= number_format($p['valor'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-<?= $p['status'] ? 'success' : 'danger' ?>">
                                        <?= $p['status'] ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=procedimentos/cadastrar&id=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=procedimentos/vincular&procedimento_id=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-primary" title="Vincular a Clínicas">
                                        <i class="fas fa-link"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger excluir-item" 
                                       data-id="<?= $p['id'] ?>" data-tipo="procedimento" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este procedimento?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <a href="#" id="btn-confirmar-exclusao" class="btn btn-danger">Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializa a tabela com DataTables
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        }
    });
    
    // Configura o modal de confirmação
    $('.excluir-item').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        
        $('#btn-confirmar-exclusao').attr('href', 'ajax/excluir.php?tipo=' + tipo + '&id=' + id);
        $('#modalConfirmacao').modal('show');
    });
});
</script>