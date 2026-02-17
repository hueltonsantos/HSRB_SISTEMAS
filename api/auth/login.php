<?php
/**
 * Endpoint de Login
 * POST /api/auth/login
 * 
 * Body: { "email": "user@example.com", "senha": "password" }
 * Response: { "success": true, "data": { "token": "...", "user": {...} } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

// Obter dados da requisição
$data = getRequestData();

// Validar campos obrigatórios
$errors = ApiValidator::validateRequired($data, ['email', 'senha']);
if ($errors) {
    ApiResponse::error('Dados inválidos', 400, $errors);
}

$email = ApiValidator::sanitizeString($data['email']);
$senha = $data['senha'];

// Validar formato do email
if (!ApiValidator::validateEmail($email)) {
    ApiResponse::error('Email inválido', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar usuário pelo email
    $stmt = $pdo->prepare("
        SELECT u.*, p.nome as perfil_nome 
        FROM usuarios u 
        LEFT JOIN perfis p ON u.perfil_id = p.id 
        WHERE u.email = ? AND u.status = 1
    ");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar se o usuário existe e senha é válida
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        ApiResponse::error('Email ou senha inválidos', 401);
    }
    
    // Buscar permissões do perfil
    $stmtPerm = $pdo->prepare("
        SELECT pm.chave 
        FROM permissoes pm
        JOIN perfil_permissoes pp ON pp.permissao_id = pm.id
        WHERE pp.perfil_id = ?
    ");
    $stmtPerm->execute([$usuario['perfil_id']]);
    $permissoes = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);
    
    // Preparar payload do token
    $tokenPayload = [
        'user_id' => $usuario['id'],
        'email' => $usuario['email'],
        'perfil_id' => $usuario['perfil_id'],
        'clinica_id' => $usuario['clinica_id'],
        'permissoes' => $permissoes
    ];
    
    // Gerar tokens
    $accessToken = JWT::encode($tokenPayload);
    $refreshToken = JWT::generateRefreshToken($usuario['id']);
    
    // Atualizar último acesso
    $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    
    // Registrar log de login
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, ip, user_agent, data_hora) 
            VALUES (?, ?, 'login', 'auth', 'Login via API mobile', ?, ?, NOW())
        ");
        $stmtLog->execute([
            $usuario['id'],
            $usuario['nome'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    // Preparar dados do usuário para resposta
    $userData = [
        'id' => $usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email'],
        'nivel_acesso' => $usuario['nivel_acesso'] ?? 'recepcionista',
        'perfil_id' => $usuario['perfil_id'],
        'perfil_nome' => $usuario['perfil_nome'],
        'clinica_id' => $usuario['clinica_id'],
        'foto' => $usuario['foto'] ?? null,
        'status' => (int)($usuario['status'] ?? 1),
        'permissoes' => $permissoes
    ];
    
    // Retornar resposta de sucesso
    ApiResponse::success([
        'token' => $accessToken,
        'refresh_token' => $refreshToken,
        'user' => $userData
    ], 'Login realizado com sucesso');
    
} catch (PDOException $e) {
    error_log("Erro no login API: " . $e->getMessage());
    ApiResponse::serverError('Erro ao processar login');
}
