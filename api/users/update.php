<?php
/**
 * Endpoint para atualizar um usuário
 * PUT /api/users/update?id=1
 * 
 * Headers: Authorization: Bearer <token>
 * Body: { "nome": "...", "email": "...", "perfil_id": 1, "status": 1 }
 * Response: { "success": true, "message": "Usuário atualizado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas PUT é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'user_manage');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do usuário não fornecido', 400);
}

$userId = (int)$_GET['id'];

// Obter dados do body
$data = getJsonInput();

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se usuário existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuarioAntigo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuarioAntigo) {
        ApiResponse::notFound('Usuário não encontrado');
    }
    
    // Construir query de atualização dinamicamente
    $updates = [];
    $updateParams = [];
    
    if (isset($data['nome'])) {
        $updates[] = 'nome = ?';
        $updateParams[] = trim($data['nome']);
    }
    
    if (isset($data['email'])) {
        $email = trim($data['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ApiResponse::error('Email inválido', 400);
        }
        
        // Verificar se email já existe (exceto para o próprio usuário)
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmtCheck->execute([$email, $userId]);
        if ($stmtCheck->fetch()) {
            ApiResponse::error('Email já cadastrado para outro usuário', 400);
        }
        
        $updates[] = 'email = ?';
        $updateParams[] = $email;
    }
    
    if (isset($data['senha']) && !empty($data['senha'])) {
        $updates[] = 'senha = ?';
        $updateParams[] = password_hash($data['senha'], PASSWORD_DEFAULT);
    }
    
    if (isset($data['perfil_id'])) {
        $updates[] = 'perfil_id = ?';
        $updateParams[] = $data['perfil_id'] ? (int)$data['perfil_id'] : null;
    }
    
    if (isset($data['clinica_id'])) {
        $updates[] = 'clinica_id = ?';
        $updateParams[] = $data['clinica_id'] ? (int)$data['clinica_id'] : null;
    }
    
    if (isset($data['parent_id'])) {
        $updates[] = 'parent_id = ?';
        $updateParams[] = $data['parent_id'] ? (int)$data['parent_id'] : null;
    }
    
    if (isset($data['nivel_acesso'])) {
        $niveisPermitidos = ['admin', 'recepcionista', 'medico'];
        if (!in_array($data['nivel_acesso'], $niveisPermitidos)) {
            ApiResponse::error('Nível de acesso inválido', 400);
        }
        $updates[] = 'nivel_acesso = ?';
        $updateParams[] = $data['nivel_acesso'];
    }
    
    if (isset($data['foto'])) {
        $updates[] = 'foto = ?';
        $updateParams[] = $data['foto'] ? trim($data['foto']) : null;
    }
    
    if (isset($data['status'])) {
        $updates[] = 'status = ?';
        $updateParams[] = (int)$data['status'];
    }
    
    if (empty($updates)) {
        ApiResponse::error('Nenhum campo para atualizar', 400);
    }
    
    // Executar atualização
    $updateParams[] = $userId;
    $sql = "UPDATE usuarios SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);
    
    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, dados_novos, ip, user_agent, data_hora) 
            VALUES (?, ?, 'editar', 'usuarios', 'Usuário atualizado via API mobile', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $userId,
            json_encode($usuarioAntigo, JSON_UNESCAPED_UNICODE),
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    ApiResponse::success(null, 'Usuário atualizado com sucesso');
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar usuário: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar usuário');
}
