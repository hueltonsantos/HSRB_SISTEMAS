<?php
/**
 * Endpoint para atualizar um perfil
 * PUT /api/profiles/update?id=1
 * Body: { "nome": "...", "descricao": "...", "permissoes": [1, 2, 3], "status": 1 }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'role_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do perfil não fornecido', 400);
}

$perfilId = (int)$_GET['id'];
$data = getJsonInput();

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM perfis WHERE id = ?");
    $stmt->execute([$perfilId]);
    $perfilAntigo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$perfilAntigo) {
        ApiResponse::notFound('Perfil não encontrado');
    }
    
    $pdo->beginTransaction();
    
    $updates = [];
    $updateParams = [];
    
    if (isset($data['nome'])) {
        $nome = trim($data['nome']);
        $stmtCheck = $pdo->prepare("SELECT id FROM perfis WHERE nome = ? AND id != ?");
        $stmtCheck->execute([$nome, $perfilId]);
        if ($stmtCheck->fetch()) {
            $pdo->rollBack();
            ApiResponse::error('Perfil com este nome já existe', 400);
        }
        $updates[] = 'nome = ?';
        $updateParams[] = $nome;
    }
    
    if (isset($data['descricao'])) {
        $updates[] = 'descricao = ?';
        $updateParams[] = trim($data['descricao']);
    }
    
    if (isset($data['status'])) {
        $updates[] = 'status = ?';
        $updateParams[] = (int)$data['status'];
    }
    
    if (!empty($updates)) {
        $updateParams[] = $perfilId;
        $sql = "UPDATE perfis SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateParams);
    }
    
    // Atualizar permissões se fornecidas
    if (isset($data['permissoes']) && is_array($data['permissoes'])) {
        // Remover permissões antigas
        $stmt = $pdo->prepare("DELETE FROM perfil_permissoes WHERE perfil_id = ?");
        $stmt->execute([$perfilId]);
        
        // Inserir novas permissões
        if (!empty($data['permissoes'])) {
            $stmtPerm = $pdo->prepare("
                INSERT INTO perfil_permissoes (perfil_id, permissao_id) 
                VALUES (?, ?)
            ");
            foreach ($data['permissoes'] as $permissaoId) {
                $stmtPerm->execute([$perfilId, (int)$permissaoId]);
            }
        }
    }
    
    $pdo->commit();
    
    // Log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, dados_novos, ip, user_agent, data_hora) 
            VALUES (?, ?, 'editar', 'perfis', 'Perfil atualizado via API mobile', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $perfilId,
            json_encode($perfilAntigo, JSON_UNESCAPED_UNICODE),
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {}
    
    ApiResponse::success(null, 'Perfil atualizado com sucesso');
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar perfil');
}
