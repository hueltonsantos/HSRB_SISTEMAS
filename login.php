<?php
/**
 * Página de login do sistema
 */
require_once 'config.php';

<<<<<<< HEAD
=======
// Verificar se precisa de configuração inicial (primeiro acesso sem admin)
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
try {
    $pdoCheck = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $stmtCheck = $pdoCheck->prepare("SELECT COUNT(*) as total FROM usuarios WHERE perfil_id = 1");
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
<<<<<<< HEAD
    if ((int) $resultCheck['total'] === 0) {
=======
    if ((int)$resultCheck['total'] === 0) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        header('Location: setup.php');
        exit;
    }
    $pdoCheck = null;
} catch (PDOException $e) {
<<<<<<< HEAD
}

=======
    // Tabela pode não existir ainda - ignora
}

// Verificar se já está logado
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$mensagem = '';

<<<<<<< HEAD
if (isset($_SESSION['setup_success'])) {
    $mensagem = '<div class="alert alert-success">
=======
// Mensagem de sucesso da configuração inicial
if (isset($_SESSION['setup_success'])) {
    $mensagem = '<div class="alert alert-success" style="background-color: #d4edda; color: #155724; border-radius: 15px; padding: 15px 20px; border: none;">
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        <i class="fas fa-check-circle mr-2"></i>
        Sistema configurado com sucesso! Faça login com as credenciais cadastradas.
    </div>';
    unset($_SESSION['setup_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

    if (empty($email) || empty($senha)) {
        $mensagem = '<div class="alert alert-danger">Por favor, preencha todos os campos.</div>';
    } else {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
<<<<<<< HEAD

=======
            
            // Buscar usuário pelo email com perfil
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
            $stmt = $pdo->prepare("
                SELECT u.*, p.nome as perfil_nome 
                FROM usuarios u 
                LEFT JOIN perfis p ON u.perfil_id = p.id 
                WHERE u.email = ? AND u.status = 1
            ");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['perfil_id'] = $usuario['perfil_id'];
                $_SESSION['perfil_nome'] = $usuario['perfil_nome'];
                $_SESSION['clinica_id'] = $usuario['clinica_id'];
                $_SESSION['usuario_foto'] = isset($usuario['foto']) ? $usuario['foto'] : null;

<<<<<<< HEAD
=======
                // Buscar permissões do perfil
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                $stmtPerm = $pdo->prepare("
                    SELECT pm.chave 
                    FROM permissoes pm
                    JOIN perfil_permissoes pp ON pp.permissao_id = pm.id
                    WHERE pp.perfil_id = ?
                ");
                $stmtPerm->execute([$usuario['perfil_id']]);
                $_SESSION['permissoes'] = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);

<<<<<<< HEAD
                $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
                $stmt->execute([$usuario['id']]);

=======
                // Atualizar data do último acesso
                $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
                $stmt->execute([$usuario['id']]);

                // Registrar log de login (ignora se tabela não existir)
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                try {
                    $stmtLog = $pdo->prepare("INSERT INTO logs_sistema (usuario_id, usuario_nome, acao, modulo, descricao, ip, user_agent, data_hora) VALUES (?, ?, 'login', 'auth', 'Login realizado com sucesso', ?, ?, NOW())");
                    $stmtLog->execute([
                        $usuario['id'],
                        $usuario['nome'],
                        $_SERVER['REMOTE_ADDR'] ?? null,
                        $_SERVER['HTTP_USER_AGENT'] ?? null
                    ]);
                } catch (Exception $e) {
<<<<<<< HEAD
                }

=======
                    // Ignora erro de log - tabela pode não existir ainda
                }

                // Redirecionar para a página inicial
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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
<<<<<<< HEAD
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
=======
    
    <!-- Estilos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- SB Admin 2 CDN -->
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgb(255, 255, 255);
            --primary-color-light: rgb(60, 89, 219);
            --secondary-color: rgb(25, 48, 148);
        }

        body {
            background: linear-gradient(135deg, #0d1b4b 0%, #1a2f7a 50%, #0d1b4b 100%);
            height: 100vh;
            overflow: hidden;
        }

        /* ===== CANVAS MATRIX ===== */
        #matrix-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.55;
        }

        /* ===== LOGIN WRAPPER ===== */
        .login-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(78, 115, 223, 0.4), 0 15px 35px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border: 1px solid rgba(78, 115, 223, 0.3);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 60px rgba(78, 115, 223, 0.5), 0 25px 50px rgba(0, 0, 0, 0.4);
        }

        .bg-login-image {
<<<<<<< HEAD
=======
            /* Imagem de fundo via CDN ou cor sólida */
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
            background: url('https://images.unsplash.com/photo-1519494026892-8095a61a7c79?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80') center center no-repeat;
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
            background: rgba(13, 27, 75, 0.65);
        }

        .login-content {
            padding: 3rem !important;
        }

        .login-logo {
            font-size: 80px;
            color: white;
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
            background: linear-gradient(to right, #4e73df, #224abe);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #224abe, #4e73df);
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(78, 115, 223, 0.5);
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        @media (max-width: 992px) {
            .login-content {
                padding: 2rem !important;
            }
            .card-body {
                padding: 0;
            }
        }

        @media (max-width: 768px) {
            .bg-login-image {
<<<<<<< HEAD
                display: none !important;
            }

=======
                display: none !important; /* Hide image completely on mobile to save space */
            }
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
            .card {
                margin-top: 2rem !important;
                margin-bottom: 2rem !important;
            }
<<<<<<< HEAD

            .login-content {
                padding: 1.5rem !important;
=======
            .login-content {
                padding: 1.5rem !important;
            }
            .h4 {
                font-size: 1.25rem;
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

    <!-- Canvas Matrix -->
    <canvas id="matrix-canvas"></canvas>

    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="card o-hidden border-0 my-5">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                    <div class="d-flex h-100 align-items-center justify-content-center">
<<<<<<< HEAD
=======
                                        <!-- Logo substituto usando ícone -->
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                                        <i class="fas fa-clinic-medical login-logo"></i>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="login-content">
                                        <div class="text-center fade-in">
                                            <h1 class="h4 text-gray-900 mb-4 font-weight-bold">
                                                Bem-vindo ao <br><?php echo SYSTEM_NAME; ?>
                                            </h1>
                                        </div>

                                        <?php if ($mensagem): ?>
                                            <div class="fade-in delay-1"><?php echo $mensagem; ?></div>
                                        <?php endif; ?>

                                        <form class="user" method="POST" action="login.php">
                                            <div class="input-group mb-4 fade-in delay-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-envelope text-gray-500"></i>
                                                    </span>
                                                </div>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Digite seu email" required>
                                            </div>

                                            <div class="input-group mb-4 fade-in delay-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-lock text-gray-500"></i>
                                                    </span>
                                                </div>
                                                <input type="password" class="form-control" name="senha"
                                                    placeholder="Senha" required>
                                            </div>

                                            <div class="fade-in delay-3">
                                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                                    <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                                                </button>
                                            </div>
                                        </form>
<<<<<<< HEAD

                                        <div class="mt-3 fade-in delay-3">
                                            <div class="text-center mb-2">
                                                <small class="text-muted font-weight-bold">Acesso de
                                                    demonstração</small>
                                            </div>
                                            <div class="bg-light rounded p-2 text-center"
                                                style="border: 1px dashed #d1d3e2; cursor: pointer;"
                                                onclick="document.querySelector('[name=email]').value='hsrbsistemas@gmail.com'; document.querySelector('[name=senha]').value='123Mudar@';"
                                                title="Clique para preencher">
                                                <small class="d-block text-gray-600"><i
                                                        class="fas fa-envelope mr-1"></i> hsrbsistemas@gmail.com</small>
                                                <small class="d-block text-gray-600"><i class="fas fa-key mr-1"></i>
                                                    123Mudar@</small>
=======
                                        
                                        <!-- Credenciais de teste -->
                                        <div class="mt-3 fade-in delay-3">
                                            <div class="text-center mb-2">
                                                <small class="text-muted font-weight-bold">Acesso de demonstração</small>
                                            </div>
                                            <div class="bg-light rounded p-2 text-center" style="border: 1px dashed #d1d3e2; cursor: pointer;" onclick="document.querySelector('[name=email]').value='hsrbsistemas@gmail.com'; document.querySelector('[name=senha]').value='123Mudar@';" title="Clique para preencher">
                                                <small class="d-block text-gray-600"><i class="fas fa-envelope mr-1"></i> hsrbsistemas@gmail.com</small>
                                                <small class="d-block text-gray-600"><i class="fas fa-key mr-1"></i> 123Mudar@</small>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                                            </div>
                                        </div>

                                        <div class="text-center mt-4 fade-in delay-3">
                                            <p class="text-muted small">
<<<<<<< HEAD
                                                &copy; <?php echo date('Y'); ?> <?php echo SYSTEM_NAME; ?>. Todos os
                                                direitos reservados.
=======
                                                &copy; <?php echo date('Y'); ?> <?php echo SYSTEM_NAME; ?>. Todos os direitos reservados.
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
=======
    <!-- Scripts via CDN -->
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
<<<<<<< HEAD

    <script>
        // ===== MATRIX RAIN =====
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');

        // Caracteres que vão cair — mistura de dados do sistema com chars técnicos
        const chars = [
            'H', 'S', 'R', 'B', '_', 'S', 'I', 'S', 'T', 'E', 'M', 'A', 'S',
            '7', '7', '9', '9', '9', '8', '8', '2', '9', '3', '0',
            'S', 'U', 'P', 'O', 'R', 'T', 'E', 'T', 'I',
            '0', '1', '<', '>', '/', '|', '{', '}', '#', '@', '!',
            'P', 'H', 'P', 'M', 'Y', 'S', 'Q', 'L',
            '5', '5', '7', '7', '█', '░', '▓'
        ];

        // Cores do tema — azul do sistema
        const coresMatrix = [
            '#4e73df',  // azul primário
            '#6f8de8',  // azul claro
            '#4f70d4ff',  // azul escuro
            '#a0b4f5',  // azul bem claro (destaque)
            '#ffffff',  // branco (ponta da coluna)
        ];

        let W, H, colunas, drops;
        const FONT_SIZE = 14;

        function resize() {
            W = canvas.width = window.innerWidth;
            H = canvas.height = window.innerHeight;
            colunas = Math.floor(W / FONT_SIZE);
            drops = new Array(colunas).fill(0).map(() => Math.random() * -100);
        }
        resize();
        window.addEventListener('resize', resize);

        // Velocidade de cada coluna (aleatória)
        const speeds = [];
        function initSpeeds() {
            for (let i = 0; i < colunas; i++) {
                speeds[i] = Math.random() * 0.1 + 0.1; // 0.3 ~ 0.8
            }
        }
        initSpeeds();

        // Brilho da ponta de cada coluna
        const brightness = [];
        function initBrightness() {
            for (let i = 0; i < colunas; i++) {
                brightness[i] = Math.random();
            }
        }
        initBrightness();

        function draw() {
            // Fade suave no fundo — deixa rastro
            ctx.fillStyle = 'rgba(47, 57, 90, 0.08)';
            ctx.fillRect(0, 0, W, H);

            ctx.font = `${FONT_SIZE}px "Courier New", monospace`;

            for (let i = 0; i < colunas; i++) {
                const char = chars[Math.floor(Math.random() * chars.length)];
                const x = i * FONT_SIZE;
                const y = drops[i] * FONT_SIZE;

                // Ponta da coluna = branco brilhante
                if (drops[i] > 0) {
                    ctx.fillStyle = '#ffffff';
                    ctx.shadowColor = '#a0b4f5';
                    ctx.shadowBlur = 8;
                    ctx.fillText(char, x, y);
                    ctx.shadowBlur = 0;
                }

                // Caractere logo abaixo da ponta = azul claro
                if (drops[i] > 1) {
                    ctx.fillStyle = '#a0b4f5';
                    ctx.fillText(
                        chars[Math.floor(Math.random() * chars.length)],
                        x, y - FONT_SIZE
                    );
                }

                // Resto da coluna = azul médio/escuro com fade
                const cor = coresMatrix[Math.floor(Math.random() * (coresMatrix.length - 2))];
                ctx.fillStyle = cor;
                ctx.globalAlpha = 0.7;
                ctx.fillText(
                    chars[Math.floor(Math.random() * chars.length)],
                    x, y - FONT_SIZE * 2
                );
                ctx.globalAlpha = 1;

                // Avança a gota
                drops[i] += speeds[i];

                // Reset aleatório para criar efeito de comprimento variado
                if (drops[i] * FONT_SIZE > H && Math.random() > 0.975) {
                    drops[i] = Math.random() * -50;
                    speeds[i] = Math.random() * 0.5 + 0.3;
                }
            }

            requestAnimationFrame(draw);
        }
        draw();
    </script>
=======
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
</body>

</html>