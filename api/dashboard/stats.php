<?php
/**
 * Endpoint de Estatísticas do Dashboard
 * GET /api/dashboard/stats
 * 
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "stats": {...} } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação
$user = requireAuth();

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Filtrar por clínica se o usuário não for admin
    $clinicaFilter = '';
    $params = [];
    
    if ($user['clinica_id']) {
        $clinicaFilter = ' WHERE clinica_id = ?';
        $params[] = $user['clinica_id'];
    }
    
    // Total de pacientes
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pacientes" . $clinicaFilter);
    $stmt->execute($params);
    $totalPacientes = $stmt->fetchColumn();
    
    // Total de agendamentos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos" . $clinicaFilter);
    $stmt->execute($params);
    $totalAgendamentos = $stmt->fetchColumn();
    
    // Agendamentos hoje
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM agendamentos
        WHERE DATE(data_consulta) = CURDATE()" .
        ($user['clinica_id'] ? " AND clinica_id = ?" : "")
    );
    $stmt->execute($params);
    $agendamentosHoje = $stmt->fetchColumn();

    // Agendamentos pendentes (status_agendamento = 'agendado')
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM agendamentos
        WHERE status_agendamento = 'agendado'" .
        ($user['clinica_id'] ? " AND clinica_id = ?" : "")
    );
    $stmt->execute($params);
    $agendamentosPendentes = $stmt->fetchColumn();

    // Total de clínicas (apenas para admin)
    $totalClinicas = 0;
    if (!$user['clinica_id']) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM clinicas_parceiras WHERE status = 1");
        $totalClinicas = $stmt->fetchColumn();
    }
    
    // Total de especialidades
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM especialidades WHERE status = 1");
    $totalEspecialidades = $stmt->fetchColumn();
    
    // Agendamentos por mês (últimos 6 meses)
    $stmt = $pdo->prepare("
        SELECT
            DATE_FORMAT(data_consulta, '%Y-%m') as mes,
            COUNT(*) as total
        FROM agendamentos
        WHERE data_consulta >= DATE_SUB(NOW(), INTERVAL 6 MONTH)" .
        ($user['clinica_id'] ? " AND clinica_id = ?" : "") . "
        GROUP BY mes
        ORDER BY mes ASC
    ");
    $stmt->execute($params);
    $agendamentosPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pacientes cadastrados por mês (últimos 6 meses)
    $stmt = $pdo->prepare("
        SELECT
            DATE_FORMAT(data_cadastro, '%Y-%m') as mes,
            COUNT(*) as total
        FROM pacientes
        WHERE data_cadastro >= DATE_SUB(NOW(), INTERVAL 6 MONTH)" .
        ($user['clinica_id'] ? " AND clinica_id = ?" : "") . "
        GROUP BY mes
        ORDER BY mes ASC
    ");
    $stmt->execute($params);
    $pacientesPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agendamentos recentes (últimos 5)
    $stmt = $pdo->prepare("
        SELECT
            a.id,
            a.data_consulta AS data_agendamento,
            a.hora_consulta AS hora_agendamento,
            a.status_agendamento AS status,
            p.nome as paciente_nome,
            e.nome as especialidade_nome,
            cp.nome as clinica_nome
        FROM agendamentos a
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        LEFT JOIN especialidades e ON a.especialidade_id = e.id
        LEFT JOIN clinicas_parceiras cp ON a.clinica_id = cp.id" .
        ($user['clinica_id'] ? " WHERE a.clinica_id = ?" : "") . "
        ORDER BY a.data_consulta DESC, a.hora_consulta DESC
        LIMIT 5
    ");
    $stmt->execute($params);
    $agendamentosRecentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Preparar resposta
    $stats = [
        'cards' => [
            'total_pacientes' => (int)$totalPacientes,
            'total_agendamentos' => (int)$totalAgendamentos,
            'agendamentos_hoje' => (int)$agendamentosHoje,
            'agendamentos_pendentes' => (int)$agendamentosPendentes,
            'total_clinicas' => (int)$totalClinicas,
            'total_especialidades' => (int)$totalEspecialidades
        ],
        'charts' => [
            'agendamentos_por_mes' => $agendamentosPorMes,
            'pacientes_por_mes' => $pacientesPorMes
        ],
        'recent_appointments' => $agendamentosRecentes
    ];
    
    ApiResponse::success(['stats' => $stats]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar estatísticas');
}
