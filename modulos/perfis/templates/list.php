<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Perfis de Acesso</h1>
    <a href="index.php?module=perfis&action=form" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Novo Perfil
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Perfis</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($perfis)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum perfil encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($perfis as $perfil): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($perfil['nome']); ?></td>
                                <td><?php echo htmlspecialchars($perfil['descricao']); ?></td>
                                <td>
                                    <?php if ($perfil['status']): ?>
                                        <span class="badge badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?module=perfis&action=form&id=<?php echo $perfil['id']; ?>" class="btn btn-info btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($perfil['id'] > 3): // Protect system roles ?>
                                    <a href="#" onclick="if(confirm('Tem certeza que deseja excluir?')) window.location.href='index.php?module=perfis&action=delete&id=<?php echo $perfil['id']; ?>';" class="btn btn-danger btn-sm" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
