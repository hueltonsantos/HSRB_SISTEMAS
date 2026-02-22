<?php
/**
 * Endpoint para criar um novo usuário
 * POST /api/users/create
 * 
 * Headers: Authorization: Bearer <token>
 * Body: { "nome": "...", "email": "...", "senha": "...", "perfil_id": 1, "clinica_id": null, "nivel_acesso": "admin" }
 * Response: { "success": true, "data": { "id": 123 } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'user_manage');

// Obter dados do body
$data = getJsonInput();

// Validar campos obrigatórios
$errors = ApiValidator::validateRequired($data, ['nome', 'email', 'senha', 'nivel_acesso']);
if (!empty($errors)) {
    ApiResponse::error('Campos obrigatórios faltando: ' . implode(', ', $errors), 400);
}

// Validar email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    ApiResponse::error('Email inválido', 400);
}

// Validar nível de acesso
$niveisPermitidos = ['admin', 'recepcionista', 'medico'];
if (!in_array($data['nivel_acesso'], $niveisPermitidos)) {
    ApiResponse::error('Nível de acesso inválido', 400);
}

// Extrair dados
$nome = trim($data['nome']);
$email = trim($data['email']);
$senha = password_hash($data['senha'], PASSWORD_DEFAULT);
$perfilId = isset($data['perfil_id']) ? (int)$data['perfil_id'] : null;
$clinicaId = isset($data['clinica_id']) ? (int)$data['clinica_id'] : null;
$parentId = isset($data['parent_id']) ? (int)$data['parent_id'] : null;
$nivelAcesso = $data['nivel_acesso'];
$foto = isset($data['foto']) ? trim($data['foto']) : null;

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se email já existe
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmtCheck->execute([$email]);
    if ($stmtCheck->fetch()) {
        ApiResponse::error('Email já cadastrado', 400);
    }
    
    // Inserir usuário
    $stmt = $pdo->prepare("
        INSERT INTO usuarios 
        (nome, email, senha, perfil_id, clinica_id, parent_id, nivel_acesso, foto, status, data_cadastro) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->execute([
        $nome,
        $email,
        $senha,
        $perfilId,
        $clinicaId,
        $parentId,
        $nivelAcesso,
        $foto
    ]);
    
    $userId = (int)$pdo->lastInsertId();
    
    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_novos, ip, user_agent, data_hora) 
            VALUES (?, ?, 'criar', 'usuarios', 'Novo usuário criado via API mobile', ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $userId,
            json_encode(['nome' => $nome, 'email' => $email], JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    ApiResponse::success(['id' => $userId], 'Usuário criado com sucesso', 201);
    
} catch (PDOException $e) {
    error_log("Erro ao criar usuário: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar usuário');
}
