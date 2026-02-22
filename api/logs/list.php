<?php
/**
 * Endpoint de Listagem de Logs do Sistema
 * GET /api/logs/list
 * Query params: ?page=1&limit=20&usuario_id=1&modulo=pacientes&acao=criar&data_inicio=2026-02-01
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'user_manage'); // Apenas admins

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    $usuarioId = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;
    $modulo = isset($_GET['modulo']) ? trim($_GET['modulo']) : '';
    $acao = isset($_GET['acao']) ? trim($_GET['acao']) : '';
    $dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : null;
    $dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : null;
    
    $where = [];
    $params = [];
    
    if ($usuarioId) {
        $where[] = 'usuario_id = ?';
        $params[] = $usuarioId;
    }
    
    if ($modulo) {
        $where[] = 'modulo = ?';
        $params[] = $modulo;
    }
    
    if ($acao) {
        $where[] = 'acao = ?';
        $params[] = $acao;
    }
    
    if ($dataInicio) {
        $where[] = 'DATE(data_hora) >= ?';
        $params[] = $dataInicio;
    }
    
    if ($dataFim) {
        $where[] = 'DATE(data_hora) <= ?';
        $params[] = $dataFim;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM logs_sistema $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();
    
    $stmt = $pdo->prepare("
        SELECT 
            id,
            usuario_id,
            usuario_nome,
            acao,
            modulo,
            descricao,
            registro_id,
            ip,
            user_agent,
            data_hora
        FROM logs_sistema
        $whereClause
        ORDER BY data_hora DESC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($logs as &$log) {
        $log['id'] = (int)$log['id'];
        $log['usuario_id'] = $log['usuario_id'] ? (int)$log['usuario_id'] : null;
        $log['registro_id'] = $log['registro_id'] ? (int)$log['registro_id'] : null;
    }
    
    $pages = ceil($total / $limit);
    
    ApiResponse::success([
        'items' => $logs,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao listar logs: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar logs');
}
