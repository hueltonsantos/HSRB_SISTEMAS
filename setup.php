<?php
/**
 * Página de Configuração Inicial (Setup Wizard)
 * Exibida apenas na primeira execução, quando não existe administrador cadastrado.
 */
require_once 'config.php';

// Verifica se já existe um administrador - se sim, redireciona para login
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
    if ((int)$resultCheck['total'] > 0) {
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    // Se o banco ainda não existe, continua mostrando o setup
}

require_once 'Database.php';
require_once 'Model.php';
require_once MODULES_PATH . '/usuarios/models/UsuarioModel.php';
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';

// Garante que as colunas telefone e cpf existam na tabela usuarios
// (migration_v8 roda automaticamente no Docker, mas no XAMPP precisa criar aqui)
try {
    $dbSetup = Database::getInstance();
    $dbSetup->query("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS telefone VARCHAR(20) DEFAULT NULL AFTER email");
    $dbSetup->query("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS cpf VARCHAR(14) DEFAULT NULL AFTER telefone");
} catch (Exception $e) {
    // Ignora se já existem ou se o banco não suporta IF NOT EXISTS
}

$errors = [];
$generalError = '';
$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$adminData = isset($_SESSION['setup_admin']) ? $_SESSION['setup_admin'] : [];
$clinicaData = isset($_SESSION['setup_clinica']) ? $_SESSION['setup_clinica'] : [];

// Botão Voltar: reseta para passo 1
if (isset($_POST['voltar'])) {
    $step = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['voltar'])) {
    if ($step === 1) {
        // Coleta e valida dados do administrador
        $adminData = [
            'nome'              => trim($_POST['admin_nome'] ?? ''),
            'email'             => trim($_POST['admin_email'] ?? ''),
            'senha'             => $_POST['admin_senha'] ?? '',
            'senha_confirmacao' => $_POST['admin_senha_confirmacao'] ?? '',
            'telefone'          => trim($_POST['admin_telefone'] ?? ''),
            'cpf'               => trim($_POST['admin_cpf'] ?? ''),
        ];

        if (empty($adminData['nome'])) {
            $errors['admin_nome'] = 'O nome é obrigatório';
        }
        if (empty($adminData['email'])) {
            $errors['admin_email'] = 'O e-mail é obrigatório';
        } elseif (!filter_var($adminData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['admin_email'] = 'E-mail inválido';
        }
        if (empty($adminData['senha'])) {
            $errors['admin_senha'] = 'A senha é obrigatória';
        } elseif (strlen($adminData['senha']) < 6) {
            $errors['admin_senha'] = 'A senha deve ter no mínimo 6 caracteres';
        }
        if ($adminData['senha'] !== $adminData['senha_confirmacao']) {
            $errors['admin_senha_confirmacao'] = 'As senhas não conferem';
        }
        if (!empty($adminData['cpf'])) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $adminData['cpf']);
            if (strlen($cpfLimpo) !== 11) {
                $errors['admin_cpf'] = 'CPF inválido';
            }
        }

        if (empty($errors)) {
            $_SESSION['setup_admin'] = $adminData;
            $step = 2;
        }
    } elseif ($step === 2) {
        // Coleta e valida dados da clínica
        $clinicaData = [
            'nome'         => trim($_POST['clinica_nome'] ?? ''),
            'cnpj'         => trim($_POST['clinica_cnpj'] ?? ''),
            'razao_social' => trim($_POST['clinica_razao_social'] ?? ''),
            'responsavel'  => trim($_POST['clinica_responsavel'] ?? ''),
            'endereco'     => trim($_POST['clinica_endereco'] ?? ''),
            'numero'       => trim($_POST['clinica_numero'] ?? ''),
            'bairro'       => trim($_POST['clinica_bairro'] ?? ''),
            'cidade'       => trim($_POST['clinica_cidade'] ?? ''),
            'estado'       => trim($_POST['clinica_estado'] ?? ''),
            'cep'          => trim($_POST['clinica_cep'] ?? ''),
            'telefone'     => trim($_POST['clinica_telefone'] ?? ''),
            'email'        => trim($_POST['clinica_email'] ?? ''),
        ];

        $_SESSION['setup_clinica'] = $clinicaData;

        // Validação via ClinicaModel
        $clinicaModel = new ClinicaModel();
        $clinicaValidation = $clinicaModel->validate($clinicaData);
        if (!$clinicaValidation['success']) {
            $errors = $clinicaValidation['errors'];
        }

        if (empty($errors)) {
            $adminData = $_SESSION['setup_admin'];

            try {
                $db = Database::getInstance();
                $db->beginTransaction();

                // 1. Criar clínica como tipo master
                $clinicaSaveData = $clinicaData;
                $clinicaSaveData['tipo'] = 'master';
                $clinicaSaveData['status'] = 1;
                $result = $clinicaModel->saveClinica($clinicaSaveData);

                if (!$result['success']) {
                    throw new Exception($result['message']);
                }
                $clinicaId = $result['id'];

                // 2. Criar usuário administrador vinculado à clínica
                $usuarioModel = new UsuarioModel();
                $adminId = $usuarioModel->inserir([
                    'nome'         => $adminData['nome'],
                    'email'        => $adminData['email'],
                    'senha'        => $adminData['senha'],
                    'telefone'     => $adminData['telefone'],
                    'cpf'          => preg_replace('/[^0-9]/', '', $adminData['cpf']),
                    'perfil_id'    => 1,
                    'clinica_id'   => $clinicaId,
                    'nivel_acesso' => 'admin',
                    'status'       => 1,
                ]);

                if (!$adminId) {
                    throw new Exception('Erro ao criar usuário administrador');
                }

                $db->commit();

                // Limpa dados de setup da sessão
                unset($_SESSION['setup_admin']);
                unset($_SESSION['setup_clinica']);

                // Redireciona para login com mensagem de sucesso
                $_SESSION['setup_success'] = true;
                header('Location: login.php');
                exit;

            } catch (Exception $e) {
                $db->rollBack();
                // Log do erro real para debug (não expõe ao usuário)
                error_log('Setup Wizard Error: ' . $e->getMessage());

                // Mensagens amigáveis baseadas no tipo de erro
                $msg = $e->getMessage();
                if (strpos($msg, 'Duplicate entry') !== false && strpos($msg, 'email') !== false) {
                    $generalError = 'Este e-mail já está cadastrado no sistema.';
                } elseif (strpos($msg, 'Duplicate entry') !== false && strpos($msg, 'cnpj') !== false) {
                    $generalError = 'Este CNPJ já está cadastrado no sistema.';
                } elseif (strpos($msg, 'Column not found') !== false) {
                    $generalError = 'Erro na estrutura do banco de dados. Verifique se as migrações foram aplicadas.';
                } else {
                    $generalError = 'Ocorreu um erro ao configurar o sistema. Tente novamente.';
                }
            }
        }
    }
}

// Lista de estados brasileiros
$estados = [
    'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
    'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
    'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
    'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
    'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Configuração Inicial - <?php echo SYSTEM_NAME; ?></title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgb(255, 255, 255);
            --primary-color-light: rgb(60, 89, 219);
            --secondary-color: rgb(25, 48, 148);
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-light) 100%);
            min-height: 100vh;
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(66, 77, 105, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        }

        .form-control {
            height: 45px;
            border-radius: 25px;
            padding-left: 20px;
            font-size: 0.95rem;
            border: 1px solid #d1d3e2;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(7, 28, 73, 0.25);
        }

        select.form-control {
            padding-left: 15px;
        }

        .input-group-prepend .input-group-text {
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
            border-right: none;
            background-color: white;
            padding-left: 18px;
        }

        .input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: none;
        }

        .btn-primary {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            background: linear-gradient(to right, var(--primary-color-light), var(--secondary-color));
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color-light));
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .btn-secondary {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .alert {
            border-radius: 15px;
            padding: 15px 20px;
            border: none;
        }

        .alert-danger {
            background-color: #ffe5e5;
            color: #e74a3b;
        }

        /* Stepper */
        .stepper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }

        .stepper-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .stepper-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #d1d3e2;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.4s ease;
        }

        .stepper-step.active .stepper-circle,
        .stepper-step.completed .stepper-circle {
            background: linear-gradient(135deg, var(--primary-color-light), var(--secondary-color));
            box-shadow: 0 4px 15px rgba(25, 48, 148, 0.3);
        }

        .stepper-label {
            margin-top: 10px;
            font-size: 0.85rem;
            color: #858796;
            font-weight: 500;
        }

        .stepper-step.active .stepper-label {
            color: var(--secondary-color);
            font-weight: 700;
        }

        .stepper-step.completed .stepper-label {
            color: var(--secondary-color);
        }

        .stepper-line {
            position: absolute;
            top: 25px;
            left: 25%;
            right: 25%;
            height: 3px;
            background: #d1d3e2;
            z-index: 0;
        }

        .stepper-line.completed {
            background: linear-gradient(to right, var(--primary-color-light), var(--secondary-color));
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        label {
            font-weight: 600;
            color: #5a5c69;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .invalid-feedback-custom {
            color: #e74a3b;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }
            .stepper-label {
                font-size: 0.75rem;
            }
            .stepper-circle {
                width: 40px;
                height: 40px;
                font-size: 0.95rem;
            }
            .stepper-line {
                top: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
            <div class="col-xl-8 col-lg-10 col-md-12">
                <div class="card o-hidden border-0 my-4">
                    <div class="card-body p-5">
                        <!-- Título -->
                        <div class="text-center mb-4 fade-in">
                            <i class="fas fa-clinic-medical fa-3x mb-3" style="color: var(--secondary-color);"></i>
                            <h1 class="h4 text-gray-900 font-weight-bold">Configuração Inicial</h1>
                            <p class="text-muted mb-0">Configure o sistema em poucos passos</p>
                        </div>

                        <!-- Stepper -->
                        <div class="stepper fade-in delay-1">
                            <div class="stepper-line <?php echo $step > 1 ? 'completed' : ''; ?>"></div>
                            <div class="stepper-step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                                <div class="stepper-circle">
                                    <?php if ($step > 1): ?>
                                        <i class="fas fa-check"></i>
                                    <?php else: ?>
                                        1
                                    <?php endif; ?>
                                </div>
                                <div class="stepper-label">Administrador</div>
                            </div>
                            <div class="stepper-step <?php echo $step >= 2 ? 'active' : ''; ?>">
                                <div class="stepper-circle">2</div>
                                <div class="stepper-label">Clínica</div>
                            </div>
                        </div>

                        <!-- Erros -->
                        <?php if (!empty($generalError)): ?>
                            <div class="alert alert-danger fade-in">
                                <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($generalError); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger fade-in">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Corrija os erros abaixo para continuar.
                            </div>
                        <?php endif; ?>

                        <!-- ===================== PASSO 1: ADMINISTRADOR ===================== -->
                        <?php if ($step === 1): ?>
                        <form method="POST" action="setup.php" id="formStep1" class="fade-in delay-2">
                            <input type="hidden" name="step" value="1">

                            <h5 class="text-gray-800 mb-3"><i class="fas fa-user-shield mr-2"></i>Dados do Administrador</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_nome">Nome Completo *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?php echo isset($errors['admin_nome']) ? 'is-invalid' : ''; ?>"
                                               id="admin_nome" name="admin_nome" placeholder="Seu nome completo" required
                                               value="<?php echo htmlspecialchars($adminData['nome'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['admin_nome'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['admin_nome']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin_email">E-mail *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope text-gray-400"></i></span>
                                        </div>
                                        <input type="email" class="form-control <?php echo isset($errors['admin_email']) ? 'is-invalid' : ''; ?>"
                                               id="admin_email" name="admin_email" placeholder="admin@email.com" required
                                               value="<?php echo htmlspecialchars($adminData['email'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['admin_email'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['admin_email']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_senha">Senha *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock text-gray-400"></i></span>
                                        </div>
                                        <input type="password" class="form-control <?php echo isset($errors['admin_senha']) ? 'is-invalid' : ''; ?>"
                                               id="admin_senha" name="admin_senha" placeholder="Mínimo 6 caracteres" required>
                                    </div>
                                    <?php if (isset($errors['admin_senha'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['admin_senha']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin_senha_confirmacao">Confirmar Senha *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock text-gray-400"></i></span>
                                        </div>
                                        <input type="password" class="form-control <?php echo isset($errors['admin_senha_confirmacao']) ? 'is-invalid' : ''; ?>"
                                               id="admin_senha_confirmacao" name="admin_senha_confirmacao" placeholder="Repita a senha" required>
                                    </div>
                                    <?php if (isset($errors['admin_senha_confirmacao'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['admin_senha_confirmacao']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_telefone">Telefone</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control"
                                               id="admin_telefone" name="admin_telefone" placeholder="(00) 00000-0000"
                                               value="<?php echo htmlspecialchars($adminData['telefone'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin_cpf">CPF</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?php echo isset($errors['admin_cpf']) ? 'is-invalid' : ''; ?>"
                                               id="admin_cpf" name="admin_cpf" placeholder="000.000.000-00"
                                               value="<?php echo htmlspecialchars($adminData['cpf'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['admin_cpf'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['admin_cpf']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Próximo <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <!-- ===================== PASSO 2: CLÍNICA ===================== -->
                        <?php if ($step === 2): ?>
                        <form method="POST" action="setup.php" id="formStep2" class="fade-in delay-2">
                            <input type="hidden" name="step" value="2">

                            <h5 class="text-gray-800 mb-3"><i class="fas fa-hospital mr-2"></i>Dados da Clínica</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="clinica_nome">Nome da Clínica *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-hospital text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?php echo isset($errors['nome']) ? 'is-invalid' : ''; ?>"
                                               id="clinica_nome" name="clinica_nome" placeholder="Nome da clínica" required
                                               value="<?php echo htmlspecialchars($clinicaData['nome'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['nome'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['nome']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="clinica_cnpj">CNPJ</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-file-alt text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?php echo isset($errors['cnpj']) ? 'is-invalid' : ''; ?>"
                                               id="clinica_cnpj" name="clinica_cnpj" placeholder="00.000.000/0000-00"
                                               value="<?php echo htmlspecialchars($clinicaData['cnpj'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['cnpj'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['cnpj']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="clinica_razao_social">Razão Social</label>
                                    <input type="text" class="form-control"
                                           id="clinica_razao_social" name="clinica_razao_social" placeholder="Razão social"
                                           value="<?php echo htmlspecialchars($clinicaData['razao_social'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="clinica_responsavel">Responsável</label>
                                    <input type="text" class="form-control"
                                           id="clinica_responsavel" name="clinica_responsavel" placeholder="Nome do responsável"
                                           value="<?php echo htmlspecialchars($clinicaData['responsavel'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="clinica_cep">CEP</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-pin text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?php echo isset($errors['cep']) ? 'is-invalid' : ''; ?>"
                                               id="clinica_cep" name="clinica_cep" placeholder="00000-000"
                                               value="<?php echo htmlspecialchars($clinicaData['cep'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['cep'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['cep']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="clinica_endereco">Endereço *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['endereco']) ? 'is-invalid' : ''; ?>"
                                           id="clinica_endereco" name="clinica_endereco" placeholder="Rua, Avenida..." required
                                           value="<?php echo htmlspecialchars($clinicaData['endereco'] ?? ''); ?>">
                                    <?php if (isset($errors['endereco'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['endereco']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="clinica_numero">Número</label>
                                    <input type="text" class="form-control"
                                           id="clinica_numero" name="clinica_numero" placeholder="Nº"
                                           value="<?php echo htmlspecialchars($clinicaData['numero'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="clinica_bairro">Bairro</label>
                                    <input type="text" class="form-control"
                                           id="clinica_bairro" name="clinica_bairro" placeholder="Bairro"
                                           value="<?php echo htmlspecialchars($clinicaData['bairro'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="clinica_cidade">Cidade *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['cidade']) ? 'is-invalid' : ''; ?>"
                                           id="clinica_cidade" name="clinica_cidade" placeholder="Cidade" required
                                           value="<?php echo htmlspecialchars($clinicaData['cidade'] ?? ''); ?>">
                                    <?php if (isset($errors['cidade'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['cidade']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="clinica_estado">Estado *</label>
                                    <select class="form-control <?php echo isset($errors['estado']) ? 'is-invalid' : ''; ?>"
                                            id="clinica_estado" name="clinica_estado" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($estados as $sigla => $nomeEstado): ?>
                                            <option value="<?php echo $sigla; ?>" <?php echo (isset($clinicaData['estado']) && $clinicaData['estado'] === $sigla) ? 'selected' : ''; ?>>
                                                <?php echo $nomeEstado; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['estado'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['estado']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="clinica_telefone">Telefone *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone text-gray-400"></i></span>
                                        </div>
                                        <input type="text" class="form-control <?php echo isset($errors['telefone']) ? 'is-invalid' : ''; ?>"
                                               id="clinica_telefone" name="clinica_telefone" placeholder="(00) 0000-0000" required
                                               value="<?php echo htmlspecialchars($clinicaData['telefone'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['telefone'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['telefone']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="clinica_email">E-mail</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope text-gray-400"></i></span>
                                        </div>
                                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                               id="clinica_email" name="clinica_email" placeholder="clinica@email.com"
                                               value="<?php echo htmlspecialchars($clinicaData['email'] ?? ''); ?>">
                                    </div>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback-custom"><?php echo $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" name="voltar" value="1" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check mr-2"></i> Finalizar Configuração
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <!-- Footer -->
                        <div class="text-center mt-4 fade-in delay-3">
                            <p class="text-muted small mb-0">
                                &copy; <?php echo date('Y'); ?> <?php echo SYSTEM_NAME; ?>. Todos os direitos reservados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
    $(document).ready(function() {
        // Máscaras
        $('#admin_cpf').mask('000.000.000-00');
        $('#admin_telefone').mask('(00) 00000-0000');
        $('#clinica_cnpj').mask('00.000.000/0000-00');
        $('#clinica_cep').mask('00000-000');
        $('#clinica_telefone').mask('(00) 0000-00009').focusout(function() {
            var phone = $(this).val().replace(/\D/g, '');
            if (phone.length > 10) {
                $(this).mask('(00) 00000-0000');
            } else {
                $(this).mask('(00) 0000-00009');
            }
        });

        // Auto-preenchimento de endereço via CEP (ViaCEP)
        $('#clinica_cep').blur(function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                    if (!data.erro) {
                        $('#clinica_endereco').val(data.logradouro);
                        $('#clinica_bairro').val(data.bairro);
                        $('#clinica_cidade').val(data.localidade);
                        $('#clinica_estado').val(data.uf);
                        $('#clinica_numero').focus();
                    }
                });
            }
        });

        // Validação client-side da confirmação de senha
        $('#formStep1').submit(function(e) {
            var senha = $('#admin_senha').val();
            var confirmacao = $('#admin_senha_confirmacao').val();
            if (senha !== confirmacao) {
                e.preventDefault();
                alert('As senhas não conferem!');
                $('#admin_senha_confirmacao').focus();
            }
        });
    });
    </script>
</body>
</html>
