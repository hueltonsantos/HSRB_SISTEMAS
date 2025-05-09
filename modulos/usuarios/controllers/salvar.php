<?php
// Arquivo de log para depuração
$log_file = ROOT_PATH . '/salvar_debug.log';
file_put_contents($log_file, "--- NOVA EXECUÇÃO: " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
file_put_contents($log_file, "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents($log_file, "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Garante que a sessão está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    file_put_contents($log_file, "Sessão iniciada\n", FILE_APPEND);
}

// Verifique se é método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents($log_file, "Método não é POST, redirecionando\n", FILE_APPEND);
    header('Location: index.php?modulo=usuarios&action=listar');
    exit;
}

// Captura os dados do formulário
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
$nivel_acesso = isset($_POST['nivel_acesso']) ? trim($_POST['nivel_acesso']) : '';
$status = isset($_POST['status']) ? 1 : 0;

file_put_contents($log_file, "Dados capturados: id=$id, nome=$nome, email=$email, nivel=$nivel_acesso, status=$status\n", FILE_APPEND);

// Validação básica
$erro = '';
if (empty($nome)) {
    $erro = 'O nome é obrigatório';
} elseif (empty($email)) {
    $erro = 'O e-mail é obrigatório';
} elseif (empty($nivel_acesso)) {
    $erro = 'O nível de acesso é obrigatório';
} elseif ($id == 0 && empty($senha)) {
    $erro = 'A senha é obrigatória para novos usuários';
}

if ($erro) {
    file_put_contents($log_file, "ERRO de validação: $erro\n", FILE_APPEND);
    $_SESSION['erro'] = $erro;
    if ($id) {
        header('Location: index.php?modulo=usuarios&action=editar&id=' . $id);
    } else {
        header('Location: index.php?modulo=usuarios&action=novo');
    }
    exit;
}

try {
    file_put_contents($log_file, "Tentando conectar ao banco...\n", FILE_APPEND);
    
    // Conexão direta com o banco
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    file_put_contents($log_file, "Conexão estabelecida\n", FILE_APPEND);
    
    // Novo usuário
    if ($id == 0) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso, status) VALUES (?, ?, ?, ?, ?)";
        file_put_contents($log_file, "SQL: $sql\n", FILE_APPEND);
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$nome, $email, $senha_hash, $nivel_acesso, $status]);
        
        file_put_contents($log_file, "Resultado: " . ($resultado ? "SUCESSO" : "FALHA") . "\n", FILE_APPEND);
        
        if ($resultado) {
            $novo_id = $pdo->lastInsertId();
            file_put_contents($log_file, "Novo ID: $novo_id\n", FILE_APPEND);
            $_SESSION['sucesso'] = 'Usuário cadastrado com sucesso!';
        } else {
            $erro_info = print_r($stmt->errorInfo(), true);
            file_put_contents($log_file, "Erro PDO: $erro_info\n", FILE_APPEND);
            $_SESSION['erro'] = 'Erro ao cadastrar usuário.';
        }
    } 
    // Atualização de usuário existente
    else {
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, nivel_acesso = ?, status = ? WHERE id = ?";
            $params = [$nome, $email, $senha_hash, $nivel_acesso, $status, $id];
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, nivel_acesso = ?, status = ? WHERE id = ?";
            $params = [$nome, $email, $nivel_acesso, $status, $id];
        }
        
        file_put_contents($log_file, "SQL: $sql\n", FILE_APPEND);
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute($params);
        
        file_put_contents($log_file, "Resultado: " . ($resultado ? "SUCESSO" : "FALHA") . "\n", FILE_APPEND);
        
        if ($resultado) {
            $_SESSION['sucesso'] = 'Usuário atualizado com sucesso!';
        } else {
            $erro_info = print_r($stmt->errorInfo(), true);
            file_put_contents($log_file, "Erro PDO: $erro_info\n", FILE_APPEND);
            $_SESSION['erro'] = 'Erro ao atualizar usuário.';
        }
    }
} catch (PDOException $e) {
    file_put_contents($log_file, "EXCEÇÃO PDO: " . $e->getMessage() . "\n", FILE_APPEND);
    $_SESSION['erro'] = 'Erro de banco de dados: ' . $e->getMessage();
}

file_put_contents($log_file, "Redirecionando para listagem\n", FILE_APPEND);
header('Location: index.php?modulo=usuarios&action=listar');
exit;