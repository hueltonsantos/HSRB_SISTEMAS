<?php
/**
 * Página de login do sistema
 */

// Inclui o arquivo de configuração (que já inicia a sessão)
require_once 'config.php';

// Se já estiver logado, redireciona para o painel
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

// Inclui os arquivos necessários
require_once 'Database.php';
require_once 'Model.php';
require_once 'modulos/usuarios/models/UsuarioModel.php';

$erro = '';

// Processamento do formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    
    // Validação básica
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos';
    } else {
        // Tenta realizar o login
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->validarLogin($email, $senha);
        
        if ($usuario) {
            // Login bem-sucedido
            $_SESSION['usuario'] = $usuario;
            
            // Registra o acesso
            $usuarioModel->atualizarUltimoAcesso($usuario['id']);
            
            // Redireciona para a página inicial
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Email ou senha inválidos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SYSTEM_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="assents/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assents/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 420px;
            width: 100%;
            padding: 15px;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            overflow: hidden;
        }
        .card-header {
            background: #4e73df;
            color: white;
            text-align: center;
            padding: 1.5rem;
            border-bottom: none;
        }
        .card-body {
            padding: 2rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
        }
        .btn-primary {
            padding: 0.75rem;
            background: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background: #2653d4;
            border-color: #244bd4;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-1"><?php echo SYSTEM_NAME; ?></h4>
                <p class="mb-0">Faça login para acessar o sistema</p>
            </div>
            <div class="card-body">
                <?php if ($erro): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $erro; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                placeholder="seu@email.com" required>
                        </div>
                    </div>
                    
                    <!-- Senha -->
                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="senha" name="senha" 
                                placeholder="Sua senha" required>
                        </div>
                    </div>
                    
                    <!-- Botão de Login -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Versão do sistema -->
        <div class="text-center text-muted mt-3">
            <small>Versão <?php echo SYSTEM_VERSION; ?></small>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assents/js/jquery.min.js"></script>
    <script src="assents/js/bootstrap.bundle.min.js"></script>
</body>
</html>