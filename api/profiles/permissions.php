<?php
/**
 * Endpoint para listar todas as permissões disponíveis
 * GET /api/profiles/permissions
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
// requirePermission($user, 'role_manage'); // Opcional: qualquer usuário autenticado pode precisar ver permissões? Melhor restringir.
requirePermission($user, 'role_manage');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM permissoes ORDER BY nome ASC");
    $stmt->execute();
    $permissoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Agrupar por módulo se tiver coluna modulo? Ver schema.
    // Assumindo estrutura simples por enquanto.
    
    ApiResponse::success(['items' => $permissoes]);
    
} catch (PDOException $e) {
    error_log("Erro ao listar permissões: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar permissões');
}
