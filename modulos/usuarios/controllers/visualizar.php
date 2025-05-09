<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Visualizar Usuário</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detalhes do Usuário</h6>
            <div>
                <a href="index.php?modulo=usuarios&action=editar&id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="index.php?modulo=usuarios&action=listar" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> <?php echo $usuario['id']; ?></p>
                    <p><strong>Nome:</strong> <?php echo $usuario['nome']; ?></p>
                    <p><strong>E-mail:</strong> <?php echo $usuario['email']; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Nível de Acesso:</strong> 
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
                    </p>
                    <p><strong>Status:</strong> 
                        <?php if ($usuario['status'] == 1): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inativo</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Último Acesso:</strong> 
                        <?php echo $usuario['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : 'Nunca acessou'; ?>
                    </p>
                    <p><strong>Data de Cadastro:</strong> 
                        <?php echo date('d/m/Y H:i', strtotime($usuario['data_cadastro'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>