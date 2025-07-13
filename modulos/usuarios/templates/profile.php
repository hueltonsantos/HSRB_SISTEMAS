<?php
require_once 'auth.php';
verificar_acesso(['admin']);
/**
 * Template para visualização/edição do perfil do usuário atual
 */
?>
<div class="container-fluid">
    <!-- Cabeçalho da página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Meu Perfil</h1>
    </div>
    
    <!-- Mensagem flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['flash_message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
    
    <div class="row">
        <!-- Informações de Perfil -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações do Perfil</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 4rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mt-3"><?php echo htmlspecialchars($usuario['nome']); ?></h5>
                        <p class="text-muted">
                            <?php 
                            switch ($usuario['tipo']) {
                                case 'admin':
                                    echo '<span class="badge badge-primary">Administrador</span>';
                                    break;
                                case 'medico':
                                    echo '<span class="badge badge-success">Médico</span>';
                                    break;
                                case 'atendente':
                                    echo '<span class="badge badge-info">Atendente</span>';
                                    break;
                                default:
                                    echo '<span class="badge badge-secondary">'. ucfirst($usuario['tipo']) .'</span>';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p>
                            <i class="fas fa-envelope mr-2"></i>
                            <?php echo htmlspecialchars($usuario['email']); ?>
                        </p>
                        
                        <p>
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Último acesso: 
                            <?php echo isset($usuario['ultimo_acesso']) ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : 'Nunca acessou'; ?>
                        </p>
                        
                        <p>
                            <i class="fas fa-user-clock mr-2"></i>
                            Membro desde:
                            <?php echo isset($usuario['data_criacao']) ? date('d/m/Y', strtotime($usuario['data_criacao'])) : 'N/A'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulário de Edição de Perfil -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Editar Perfil</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['geral'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $errors['geral']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="index.php?module=usuarios&action=profile" method="POST">
                        <!-- Nome -->
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control <?php echo isset($errors['nome']) ? 'is-invalid' : ''; ?>" 
                                id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                            <?php if (isset($errors['nome'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['nome']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- E-mail -->
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['email']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <h5>Alterar Senha</h5>
                        <p class="text-muted small">Preencha os campos abaixo apenas se deseja alterar sua senha.</p>
                        
                        <!-- Senha Atual -->
                        <div class="form-group">
                            <label for="senha_atual">Senha Atual</label>
                            <input type="password" class="form-control <?php echo isset($errors['senha_atual']) ? 'is-invalid' : ''; ?>" 
                                id="senha_atual" name="senha_atual">
                            <?php if (isset($errors['senha_atual'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['senha_atual']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nova Senha -->
                        <div class="form-group">
                            <label for="nova_senha">Nova Senha</label>
                            <input type="password" class="form-control <?php echo isset($errors['nova_senha']) ? 'is-invalid' : ''; ?>" 
                                id="nova_senha" name="nova_senha">
                            <?php if (isset($errors['nova_senha'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['nova_senha']; ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">A senha deve ter pelo menos 6 caracteres.</small>
                        </div>
                        
                        <!-- Confirmar Nova Senha -->
                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar Nova Senha</label>
                            <input type="password" class="form-control <?php echo isset($errors['confirmar_senha']) ? 'is-invalid' : ''; ?>" 
                                id="confirmar_senha" name="confirmar_senha">
                            <?php if (isset($errors['confirmar_senha'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['confirmar_senha']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <!-- Botões de ação -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>