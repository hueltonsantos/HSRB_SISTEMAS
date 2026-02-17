<?php
/**
 * Endpoint para atualizar um agendamento
 * PUT /api/appointments/update
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "id": 1, "status": "confirmado", "data_consulta": "2026-02-20", "hora_consulta": "15:00", "observacoes": "..." }
 * Response: { "success": true, "message": "Agendamento atualizado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas PUT é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_create');

// Obter dados da requisição
$data = getRequestData();

// Validar ID
if (!isset($data['id']) || empty($data['id'])) {
    ApiResponse::error('ID do agendamento não fornecido', 400);
}

$appointmentId = (int)$data['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se agendamento existe e pertence à clínica do usuário
    $clinicaFilter = '';
    $params = [$appointmentId];

    if ($user['clinica_id']) {
        $clinicaFilter = ' AND clinica_id = ?';
        $params[] = $user['clinica_id'];
    }

    $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?" . $clinicaFilter);
    $stmt->execute($params);
    $agendamentoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamentoExistente) {
        ApiResponse::notFound('Agendamento não encontrado');
    }

    // Preparar campos para atualização
    $updates = [];
    $updateParams = [];

    // Mapear 'status' do body para 'status_agendamento' na coluna
    if (isset($data['status'])) {
        $statusValidos = ['agendado', 'confirmado', 'realizado', 'cancelado'];
        $statusValue = trim($data['status']);
        if (!in_array($statusValue, $statusValidos)) {
            ApiResponse::error('Status inválido. Valores aceitos: agendado, confirmado, realizado, cancelado', 400);
        }
        $updates[] = 'status_agendamento = ?';
        $updateParams[] = $statusValue;
    }

    if (isset($data['data_consulta'])) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['data_consulta'])) {
            ApiResponse::error('Data da consulta inválida (formato: YYYY-MM-DD)', 400);
        }
        $updates[] = 'data_consulta = ?';
        $updateParams[] = $data['data_consulta'];
    }

    if (isset($data['hora_consulta'])) {
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $data['hora_consulta'])) {
            ApiResponse::error('Hora da consulta inválida (formato: HH:MM)', 400);
        }
        $hora = $data['hora_consulta'];
        // Garantir formato HH:MM:SS
        if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
            $hora .= ':00';
        }
        $updates[] = 'hora_consulta = ?';
        $updateParams[] = $hora;
    }

    if (isset($data['observacoes'])) {
        $updates[] = 'observacoes = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['observacoes']);
    }

    if (isset($data['paciente_id'])) {
        $updates[] = 'paciente_id = ?';
        $updateParams[] = (int)$data['paciente_id'];
    }

    if (isset($data['especialidade_id'])) {
        $updates[] = 'especialidade_id = ?';
        $updateParams[] = (int)$data['especialidade_id'];
    }

    if (isset($data['procedimento_id'])) {
        $updates[] = 'procedimento_id = ?';
        $updateParams[] = $data['procedimento_id'] ? (int)$data['procedimento_id'] : null;
    }

    if (isset($data['clinica_id']) && !$user['clinica_id']) {
        $updates[] = 'clinica_id = ?';
        $updateParams[] = (int)$data['clinica_id'];
    }

    if (empty($updates)) {
        ApiResponse::error('Nenhum campo para atualizar', 400);
    }

    // Adicionar data de atualização
    $updates[] = 'ultima_atualizacao = NOW()';

    // Executar atualização
    $updateParams[] = $appointmentId;
    $sql = "UPDATE agendamentos SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'editar', 'agendamentos', 'Agendamento atualizado via API mobile', ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $appointmentId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success(null, 'Agendamento atualizado com sucesso');

} catch (PDOException $e) {
    error_log("Erro ao atualizar agendamento: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar agendamento');
}
