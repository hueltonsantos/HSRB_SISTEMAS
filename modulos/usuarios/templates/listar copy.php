<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Usuários</h1>
    <p class="mb-4">Gerenciamento de usuários do sistema</p>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['sucesso']; 
            unset($_SESSION['sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro']; 
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="modulo" value="usuarios">
                <input type="hidden" name="action" value="listar">
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="nome" value="<?php echo isset($_GET['nome']) ? $_GET['nome'] : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="text" class="form-control" name="email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nível de Acesso</label>
                            <select class="form-control" name="nivel_acesso">
                                <option value="">Todos</option>
                                <option value="admin" <?php echo (isset($_GET['nivel_acesso']) && $_GET['nivel_acesso'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="recepcionista" <?php echo (isset($_GET['nivel_acesso']) && $_GET['nivel_acesso'] == 'recepcionista') ? 'selected' : ''; ?>>Recepcionista</option>
                                <option value="medico" <?php echo (isset($_GET['nivel_acesso']) && $_GET['nivel_acesso'] == 'medico') ? 'selected' : ''; ?>>Médico</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="">Todos</option>
                                <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Ativo</option>
                                <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="index.php?modulo=usuarios&action=listar" class="btn btn-secondary">Limpar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listagem -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Usuários Cadastrados</h6>
            <a href="index.php?modulo=usuarios&action=novo" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Usuário
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Nível de Acesso</th>
                            <th>Último Acesso</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($usuarios) > 0): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo $usuario['nome']; ?></td>
                                    <td><?php echo $usuario['email']; ?></td>
                                    <td>
                                        <?php 
                                        switch ($usuario['nivel_acesso']) {
                                            case 'admin':
                                                echo 'Administrador';
                                                break;
                                            case 'recepcionista':
                                                echo 'Recepcionista';
                                                break;
                                            case 'medico':
                                                echo 'Médico';
                                                break;
                                            default:
                                                echo $usuario['nivel_acesso'];
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $usuario['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : 'Nunca acessou'; ?></td>
                                    <td>
                                        <?php if ($usuario['status'] == 1): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?modulo=usuarios&action=visualizar&id=<?php echo $usuario['id']; ?>" class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?modulo=usuarios&action=editar&id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($usuario['id'] != $_SESSION['usuario']['id']): ?>
                                            <a href="javascript:void(0);" onclick="confirmarExclusao(<?php echo $usuario['id']; ?>)" class="btn btn-danger btn-sm" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum usuário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        window.location.href = 'index.php?modulo=usuarios&action=deletar&id=' + id;
    }
}
</script>