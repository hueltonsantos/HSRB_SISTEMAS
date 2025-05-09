<?php
/**
 * Página de login do sistema
 */
require_once 'config.php';

// Verificar se já está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Processar formulário de login
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
    
    // Validação básica
    if (empty($email) || empty($senha)) {
        $mensagem = '<div class="alert alert-danger">Por favor, preencha todos os campos.</div>';
    } else {
        try {
            // Conexão com o banco
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Buscar usuário pelo email
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND status = 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar se o usuário existe e senha é válida
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_nivel'] = $usuario['nivel_acesso'];
                
                // Atualizar data do último acesso
                $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
                $stmt->execute([$usuario['id']]);
                
                // Redirecionar para a página inicial
                header('Location: index.php');
                exit;
            } else {
                $mensagem = '<div class="alert alert-danger">Email ou senha inválidos.</div>';
            }
        } catch (PDOException $e) {
            $mensagem = '<div class="alert alert-danger">Erro ao conectar ao banco de dados: ' . $e->getMessage() . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - <?php echo SYSTEM_NAME; ?></title>
    
    <!-- Estilos -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color:rgb(255, 255, 255);
            --primary-color-light:rgb(60, 89, 219);
            --secondary-color:rgb(25, 48, 148);
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-light) 100%);
            height: 100vh;
            overflow: hidden;
        }
        
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(66, 77, 105, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(50, 50, 93, 0.15), 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .bg-login-image {
            background: url('img/clinic-background.jpg');
            background-position: center;
            background-size: cover;
            position: relative;
        }
        
        .bg-login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(23, 36, 212, 0.3);
        }
        
        .login-content {
            padding: 3rem !important;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            height: 50px;
            border-radius: 25px;
            padding-left: 20px;
            font-size: 1rem;
            border: 1px solid #d1d3e2;
            transition: all 0.2s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(7, 28, 73, 0.25);
        }
        
        .input-group-prepend .input-group-text {
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
            border-right: none;
            background-color: white;
            padding-left: 20px;
        }
        
        .input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: none;
        }
        
        .btn-primary {
            border-radius: 25px;
            padding: 12px;
            font-weight: 600;
            background: linear-gradient(to right, var(--primary-color), var(--primary-color-light));
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-color-light), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        
        .alert {
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
        }
        
        .alert-danger {
            background-color: #ffe5e5;
            color: #e74a3b;
        }
        
        @media (max-width: 992px) {
            .login-content {
                padding: 2rem !important;
            }
        }
        
        @media (max-width: 768px) {
            .bg-login-image {
                height: 200px;
            }
        }
        
        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        .delay-1 {
            animation-delay: 0.2s;
        }
        
        .delay-2 {
            animation-delay: 0.4s;
        }
        
        .delay-3 {
            animation-delay: 0.6s;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="card o-hidden border-0 my-5">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                    <div class="d-flex h-100 align-items-center justify-content-center">
                                        <img src="img/logo.png" alt="Logo" class="login-logo" style="opacity: 0; /* Oculto até você adicionar o logo */">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="login-content">
                                        <div class="text-center fade-in">
                                            <h1 class="h4 text-gray-900 mb-4 font-weight-bold">Bem-vindo ao <br><?php echo SYSTEM_NAME; ?></h1>
                                        </div>
                                        
                                        <?php if ($mensagem): ?>
                                            <div class="fade-in delay-1">
                                                <?php echo $mensagem; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <form class="user" method="POST" action="login.php">
                                            <div class="input-group mb-4 fade-in delay-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-envelope text-gray-500"></i>
                                                    </span>
                                                </div>
                                                <input type="email" class="form-control" 
                                                    name="email" placeholder="Digite seu email" required>
                                            </div>
                                            
                                            <div class="input-group mb-4 fade-in delay-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-lock text-gray-500"></i>
                                                    </span>
                                                </div>
                                                <input type="password" class="form-control" 
                                                    name="senha" placeholder="Senha" required>
                                            </div>
                                            
                                            <div class="fade-in delay-3">
                                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                                    <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <div class="text-center mt-4 fade-in delay-3">
                                            <p class="text-muted small">
                                                © <?php echo date('Y'); ?> <?php echo SYSTEM_NAME; ?>. Todos os direitos reservados.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>