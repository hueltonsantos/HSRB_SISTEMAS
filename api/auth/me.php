<?php
/**
 * Endpoint para obter dados do usuário autenticado
 * GET /api/auth/me
 * 
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "user": {...} } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação
$tokenData = requireAuth();

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar dados atualizados do usuário
    $stmt = $pdo->prepare("
        SELECT u.*, p.nome as perfil_nome 
        FROM usuarios u 
        LEFT JOIN perfis p ON u.perfil_id = p.id 
        WHERE u.id = ? AND u.status = 1
    ");
    $stmt->execute([$tokenData['user_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        ApiResponse::error('Usuário não encontrado', 404);
    }
    
    // Buscar permissões atualizadas
    $stmtPerm = $pdo->prepare("
        SELECT pm.chave 
        FROM permissoes pm
        JOIN perfil_permissoes pp ON pp.permissao_id = pm.id
        WHERE pp.perfil_id = ?
    ");
    $stmtPerm->execute([$usuario['perfil_id']]);
    $permissoes = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);
    
    // Preparar dados do usuário
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
        'telefone' => $usuario['telefone'] ?? null,
        'ultimo_acesso' => $usuario['ultimo_acesso'] ?? null,
        'permissoes' => $permissoes
    ];
    
    ApiResponse::success(['user' => $userData]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar dados do usuário');
}
