<?php
/**
 * Endpoint para relatório de agendamentos
 * GET /api/reports/appointments
 * Query params: ?data_inicio=2026-02-01&data_fim=2026-02-28&status=confirmado
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'report_view');

$dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-01');
$dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-t');
$status = isset($_GET['status']) ? $_GET['status'] : null;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $where = ['a.data_consulta BETWEEN ? AND ?'];
    $params = [$dataInicio, $dataFim];
    
    if ($status) {
        $where[] = 'a.status_agendamento = ?';
        $params[] = $status;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $where);
    
    // Total geral
    $stmtTotal = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM agendamentos a
        $whereClause
    ");
    $stmtTotal->execute($params);
    $totalGeral = (int)$stmtTotal->fetchColumn();

    // Total por status (retornar como mapa chave-valor)
    $stmt = $pdo->prepare("
        SELECT
            status_agendamento,
            COUNT(*) AS total
        FROM agendamentos a
        $whereClause
        GROUP BY status_agendamento
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $porStatus = [];
    foreach ($rows as $row) {
        $porStatus[$row['status_agendamento']] = (int)$row['total'];
    }

    // Total por dia
    $stmt = $pdo->prepare("
        SELECT
            DATE(data_consulta) AS data,
            COUNT(*) AS total
        FROM agendamentos a
        $whereClause
        GROUP BY DATE(data_consulta)
        ORDER BY data ASC
    ");
    $stmt->execute($params);
    $porDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ApiResponse::success([
        'periodo' => [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ],
        'total' => $totalGeral,
        'por_status' => $porStatus,
        'por_dia' => $porDia
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao gerar relatório de agendamentos: " . $e->getMessage());
    ApiResponse::serverError('Erro ao gerar relatório');
}
