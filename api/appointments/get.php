<?php
/**
 * Endpoint para obter detalhes de um agendamento
 * GET /api/appointments/get?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "appointment": {...} } }
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

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do agendamento não fornecido', 400);
}

$appointmentId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Construir query com filtro de clínica se necessário
    $clinicaFilter = '';
    $params = [$appointmentId];

    if ($user['clinica_id']) {
        $clinicaFilter = ' AND a.clinica_id = ?';
        $params[] = $user['clinica_id'];
    }

    // Buscar agendamento com JOINs completos
    $stmt = $pdo->prepare("
        SELECT
            a.id,
            a.paciente_id,
            a.clinica_id,
            a.especialidade_id,
            a.procedimento_id,
            a.data_consulta,
            a.hora_consulta,
            a.status_agendamento AS status,
            a.observacoes,
            a.data_agendamento,
            a.ultima_atualizacao,
            p.nome AS paciente_nome,
            p.cpf AS paciente_cpf,
            p.celular AS paciente_telefone,
            p.email AS paciente_email,
            cp.nome AS clinica_nome,
            e.nome AS especialidade_nome,
            vp.procedimento AS procedimento_nome,
            vp.valor_paciente AS procedimento_valor
        FROM agendamentos a
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        LEFT JOIN clinicas_parceiras cp ON a.clinica_id = cp.id
        LEFT JOIN especialidades e ON a.especialidade_id = e.id
        LEFT JOIN valores_procedimentos vp ON a.procedimento_id = vp.id
        WHERE a.id = ?" . $clinicaFilter
    );
    $stmt->execute($params);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamento) {
        ApiResponse::notFound('Agendamento não encontrado');
    }

    // Formatar dados
    $agendamento['id'] = (int)$agendamento['id'];
    $agendamento['paciente_id'] = (int)$agendamento['paciente_id'];
    $agendamento['clinica_id'] = (int)$agendamento['clinica_id'];
    $agendamento['especialidade_id'] = (int)$agendamento['especialidade_id'];
    $agendamento['procedimento_id'] = $agendamento['procedimento_id'] ? (int)$agendamento['procedimento_id'] : null;
    $agendamento['procedimento_valor'] = $agendamento['procedimento_valor'] ? (float)$agendamento['procedimento_valor'] : null;

    ApiResponse::success(['appointment' => $agendamento]);

} catch (PDOException $e) {
    error_log("Erro ao buscar agendamento: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar agendamento');
}
