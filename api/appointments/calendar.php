<?php
/**
 * Endpoint de Calendário de Agendamentos
 * GET /api/appointments/calendar?mes=2026-02
 *
 * Retorna contagem de agendamentos por dia para um determinado mês
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "mes": "2026-02", "dias": [{ "dia": "2026-02-01", "total": 5 }, ...] } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_view');

// Validar parâmetro mês
if (!isset($_GET['mes']) || empty($_GET['mes'])) {
    ApiResponse::error('Parâmetro "mes" não fornecido (formato: YYYY-MM)', 400);
}

$mes = trim($_GET['mes']);

// Validar formato YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
    ApiResponse::error('Formato de mês inválido (formato: YYYY-MM)', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Construir query com filtro de clínica
    $where = ["DATE_FORMAT(data_consulta, '%Y-%m') = ?"];
    $params = [$mes];

    // Filtrar por clínica do usuário se necessário
    if ($user['clinica_id']) {
        $where[] = 'clinica_id = ?';
        $params[] = $user['clinica_id'];
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    // Buscar contagem por dia
    $stmt = $pdo->prepare("
        SELECT
            DATE(data_consulta) AS dia,
            COUNT(*) AS total
        FROM agendamentos
        $whereClause
        GROUP BY dia
        ORDER BY dia ASC
    ");
    $stmt->execute($params);
    $dias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados
    foreach ($dias as &$dia) {
        $dia['total'] = (int)$dia['total'];
    }

    ApiResponse::success([
        'mes' => $mes,
        'calendar' => $dias
    ]);

} catch (PDOException $e) {
    error_log("Erro ao buscar calendário de agendamentos: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar calendário de agendamentos');
}
