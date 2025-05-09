<?php
/**
 * Template para confirmação de exclusão de usuário
 */
?>
<div class="container-fluid">
    <!-- Cabeçalho da página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Excluir Usuário</h1>
        <a href="index.php?module=usuarios" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Voltar
        </a>
    </div>
    
    <!-- Confirmação de exclusão -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Confirmação de Exclusão</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Atenção!</strong> Esta ação não pode ser desfeita.
            </div>
            
            <p class="mb-4">
                Você está prestes a excluir o usuário <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong> 
                (<?php echo htmlspecialchars($usuario['email']); ?>).
            </p>
            
            <form action="index.php?module=usuarios&action=delete&id=<?php echo $usuario['id']; ?>" method="POST">
                <input type="hidden" name="confirm_delete" value="1">
                
                <div class="form-group text-right">
                    <a href="index.php?module=usuarios" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
</div>