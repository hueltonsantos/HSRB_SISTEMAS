<?php
/**
 * Endpoint para criar um novo perfil
 * POST /api/profiles/create
 * Body: { "nome": "...", "descricao": "...", "permissoes": [1, 2, 3] }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'role_manage');

$data = getJsonInput();

$errors = ApiValidator::validateRequired($data, ['nome']);
if (!empty($errors)) {
    ApiResponse::error('Campos obrigatórios faltando: ' . implode(', ', $errors), 400);
}

$nome = trim($data['nome']);
$descricao = isset($data['descricao']) ? trim($data['descricao']) : null;
$permissoes = isset($data['permissoes']) && is_array($data['permissoes']) ? $data['permissoes'] : [];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se nome já existe
    $stmtCheck = $pdo->prepare("SELECT id FROM perfis WHERE nome = ?");
    $stmtCheck->execute([$nome]);
    if ($stmtCheck->fetch()) {
        ApiResponse::error('Perfil com este nome já existe', 400);
    }
    
    $pdo->beginTransaction();
    
    // Inserir perfil
    $stmt = $pdo->prepare("
        INSERT INTO perfis (nome, descricao, status) 
        VALUES (?, ?, 1)
    ");
    $stmt->execute([$nome, $descricao]);
    $perfilId = (int)$pdo->lastInsertId();
    
    // Inserir permissões
    if (!empty($permissoes)) {
        $stmtPerm = $pdo->prepare("
            INSERT INTO perfil_permissoes (perfil_id, permissao_id) 
            VALUES (?, ?)
        ");
        foreach ($permissoes as $permissaoId) {
            $stmtPerm->execute([$perfilId, (int)$permissaoId]);
        }
    }
    
    $pdo->commit();
    
    // Log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_novos, ip, user_agent, data_hora) 
            VALUES (?, ?, 'criar', 'perfis', 'Novo perfil criado via API mobile', ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $perfilId,
            json_encode(['nome' => $nome, 'permissoes' => $permissoes], JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {}
    
    ApiResponse::success(['id' => $perfilId], 'Perfil criado com sucesso', 201);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao criar perfil: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar perfil');
}
