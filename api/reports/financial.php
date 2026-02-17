<?php
/**
 * Endpoint para relatório financeiro
 * GET /api/reports/financial
 * Query params: ?data_inicio=2026-02-01&data_fim=2026-02-28&clinica_id=1
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
$clinicaId = isset($_GET['clinica_id']) ? (int)$_GET['clinica_id'] : null;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $where = ['a.data_consulta BETWEEN ? AND ?'];
    $params = [$dataInicio, $dataFim];
    
    if ($clinicaId) {
        $where[] = 'a.clinica_id = ?';
        $params[] = $clinicaId;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $where);
    
    // Resumo financeiro agregado
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total_agendamentos,
            COALESCE(SUM(a.valor_total), 0) AS total_receita
        FROM agendamentos a
        $whereClause
    ");
    $stmt->execute($params);
    $totais = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calcular repasse a partir dos procedimentos vinculados
    $stmtRepasse = $pdo->prepare("
        SELECT COALESCE(SUM(vp.valor_repasse), 0) AS total_repasse
        FROM agendamentos a
        INNER JOIN agendamento_procedimentos ap ON a.id = ap.agendamento_id
        INNER JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
        $whereClause
    ");
    $stmtRepasse->execute($params);
    $repasseRow = $stmtRepasse->fetch(PDO::FETCH_ASSOC);
    $totalRepasse = (float)($repasseRow['total_repasse'] ?? 0);

    $totalReceita = (float)($totais['total_receita'] ?? 0);
    $resumo = [
        'total_agendamentos' => (int)($totais['total_agendamentos'] ?? 0),
        'total_receita' => $totalReceita,
        'total_repasse' => $totalRepasse,
        'total_liquido' => $totalReceita - $totalRepasse,
    ];
    
    // Valores por clínica
    $stmt = $pdo->prepare("
        SELECT 
            c.nome AS clinica_nome,
            COUNT(*) AS total_agendamentos,
            SUM(a.valor_total) AS valor_total
        FROM agendamentos a
        LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
        $whereClause
        GROUP BY c.id, c.nome
        ORDER BY valor_total DESC
    ");
    $stmt->execute($params);
    $porClinica = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Valores por especialidade
    $stmt = $pdo->prepare("
        SELECT 
            e.nome AS especialidade_nome,
            COUNT(*) AS total_agendamentos,
            SUM(a.valor_total) AS valor_total
        FROM agendamentos a
        LEFT JOIN especialidades e ON a.especialidade_id = e.id
        $whereClause
        GROUP BY e.id, e.nome
        ORDER BY valor_total DESC
    ");
    $stmt->execute($params);
    $porEspecialidade = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ApiResponse::success([
        'periodo' => [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ],
        'resumo' => $resumo,
        'por_clinica' => $porClinica,
        'por_especialidade' => $porEspecialidade
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao gerar relatório financeiro: " . $e->getMessage());
    ApiResponse::serverError('Erro ao gerar relatório');
}
