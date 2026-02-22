<?php
/**
 * Endpoint para obter detalhes de um log
 * GET /api/logs/get?id=1
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'user_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do log não fornecido', 400);
}

$logId = (int)$_GET['id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT 
            id,
            usuario_id,
            usuario_nome,
            acao,
            modulo,
            descricao,
            registro_id,
            dados_anteriores,
            dados_novos,
            ip,
            user_agent,
            data_hora
        FROM logs_sistema
        WHERE id = ?
    ");
    $stmt->execute([$logId]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$log) {
        ApiResponse::notFound('Log não encontrado');
    }
    
    $log['id'] = (int)$log['id'];
    $log['usuario_id'] = $log['usuario_id'] ? (int)$log['usuario_id'] : null;
    $log['registro_id'] = $log['registro_id'] ? (int)$log['registro_id'] : null;
    
    // Decodificar JSONs
    if ($log['dados_anteriores']) {
        $log['dados_anteriores'] = json_decode($log['dados_anteriores'], true);
    }
    if ($log['dados_novos']) {
        $log['dados_novos'] = json_decode($log['dados_novos'], true);
    }
    
    ApiResponse::success(['log' => $log]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar log: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar log');
}
