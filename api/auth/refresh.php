<?php
/**
 * Endpoint de Refresh Token
 * POST /api/auth/refresh
 * 
 * Body: { "refresh_token": "..." }
 * Response: { "success": true, "data": { "token": "...", "refresh_token": "..." } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

// Obter dados da requisição
$data = getRequestData();

// Validar campo obrigatório
if (!isset($data['refresh_token']) || empty($data['refresh_token'])) {
    ApiResponse::error('Refresh token não fornecido', 400);
}

$refreshToken = $data['refresh_token'];

// Decodificar e validar refresh token
$payload = JWT::decode($refreshToken);

if (!$payload) {
    ApiResponse::error('Refresh token inválido ou expirado', 401);
}

// Verificar se é um refresh token
if (!isset($payload['type']) || $payload['type'] !== 'refresh') {
    ApiResponse::error('Token inválido', 401);
}

$userId = $payload['user_id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar usuário
    $stmt = $pdo->prepare("
        SELECT u.*, p.nome as perfil_nome 
        FROM usuarios u 
        LEFT JOIN perfis p ON u.perfil_id = p.id 
        WHERE u.id = ? AND u.status = 1
    ");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar se usuário existe e está ativo
    if (!$usuario) {
        ApiResponse::error('Usuário não encontrado ou inativo', 401);
    }
    
    // Buscar permissões
    $stmtPerm = $pdo->prepare("
        SELECT pm.chave 
        FROM permissoes pm
        JOIN perfil_permissoes pp ON pp.permissao_id = pm.id
        WHERE pp.perfil_id = ?
    ");
    $stmtPerm->execute([$usuario['perfil_id']]);
    $permissoes = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);
    
    // Preparar payload do novo token
    $tokenPayload = [
        'user_id' => $usuario['id'],
        'email' => $usuario['email'],
        'perfil_id' => $usuario['perfil_id'],
        'clinica_id' => $usuario['clinica_id'],
        'permissoes' => $permissoes
    ];
    
    // Gerar novos tokens
    $newAccessToken = JWT::encode($tokenPayload);
    $newRefreshToken = JWT::generateRefreshToken($usuario['id']);
    
    // Retornar resposta de sucesso
    ApiResponse::success([
        'token' => $newAccessToken,
        'refresh_token' => $newRefreshToken
    ], 'Token renovado com sucesso');
    
} catch (PDOException $e) {
    error_log("Erro no refresh token API: " . $e->getMessage());
    ApiResponse::serverError('Erro ao renovar token');
}
