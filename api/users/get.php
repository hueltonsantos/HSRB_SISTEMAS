<?php
/**
 * Endpoint para obter detalhes de um usuário
 * GET /api/users/get?id=1
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

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'user_manage');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do usuário não fornecido', 400);
}

$userId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar usuário
    $stmt = $pdo->prepare("
        SELECT
            u.id,
            u.nome,
            u.email,
            u.foto,
            u.perfil_id,
            u.clinica_id,
            u.parent_id,
            u.nivel_acesso,
            u.ultimo_acesso,
            u.status,
            u.data_cadastro,
            p.nome AS perfil_nome,
            c.nome AS clinica_nome,
            parent.nome AS parent_nome
        FROM usuarios u
        LEFT JOIN perfis p ON u.perfil_id = p.id
        LEFT JOIN clinicas_parceiras c ON u.clinica_id = c.id
        LEFT JOIN usuarios parent ON u.parent_id = parent.id
        WHERE u.id = ?
    ");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        ApiResponse::notFound('Usuário não encontrado');
    }
    
    // Formatar dados
    $usuario['id'] = (int)$usuario['id'];
    $usuario['perfil_id'] = $usuario['perfil_id'] ? (int)$usuario['perfil_id'] : null;
    $usuario['clinica_id'] = $usuario['clinica_id'] ? (int)$usuario['clinica_id'] : null;
    $usuario['parent_id'] = $usuario['parent_id'] ? (int)$usuario['parent_id'] : null;
    $usuario['status'] = (int)$usuario['status'];
    
    // Remover senha
    unset($usuario['senha']);
    
    // Buscar permissões do usuário
    if ($usuario['perfil_id']) {
        $stmtPerms = $pdo->prepare("
            SELECT p.chave, p.nome
            FROM perfil_permissoes pp
            JOIN permissoes p ON pp.permissao_id = p.id
            WHERE pp.perfil_id = ?
        ");
        $stmtPerms->execute([$usuario['perfil_id']]);
        $usuario['permissoes'] = $stmtPerms->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $usuario['permissoes'] = [];
    }
    
    ApiResponse::success(['user' => $usuario]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar usuário: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar usuário');
}
