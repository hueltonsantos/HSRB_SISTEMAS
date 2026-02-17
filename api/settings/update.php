<?php
/**
 * Endpoint para atualizar configurações do sistema
 * PUT /api/settings/update
 * Body: { "nome_clinica": "...", "telefone_clinica": "...", ... }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'user_manage'); // Apenas admins podem alterar configurações

$data = getJsonInput();

if (empty($data)) {
    ApiResponse::error('Nenhuma configuração fornecida', 400);
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        UPDATE configuracoes 
        SET valor = ?, data_atualizacao = NOW() 
        WHERE chave = ?
    ");
    
    $updated = 0;
    foreach ($data as $chave => $valor) {
        // Verificar se a chave existe
        $stmtCheck = $pdo->prepare("SELECT id FROM configuracoes WHERE chave = ?");
        $stmtCheck->execute([$chave]);
        if ($stmtCheck->fetch()) {
            $stmt->execute([$valor, $chave]);
            $updated++;
        }
    }
    
    $pdo->commit();
    
    // Log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, dados_novos, ip, user_agent, data_hora) 
            VALUES (?, ?, 'editar', 'configuracoes', 'Configurações atualizadas via API mobile', ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {}
    
    ApiResponse::success(null, "$updated configurações atualizadas com sucesso");
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao atualizar configurações: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar configurações');
}
