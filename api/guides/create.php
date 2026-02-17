<?php
/**
 * Endpoint para criar uma nova guia de encaminhamento
 * POST /api/guides/create
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "paciente_id": 1, "procedimento_id": 1, "data_agendamento": "2026-03-15", "horario_agendamento": "14:30", "observacoes": "..." }
 * Response: { "success": true, "data": { "guide_id": 1, "codigo": "GE-20260315-0042" } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_create');

// Obter dados da requisição
$data = getRequestData();

// Validar campos obrigatórios
$errors = ApiValidator::validateRequired($data, ['paciente_id', 'procedimento_id', 'data_agendamento']);
if ($errors) {
    ApiResponse::error('Dados inválidos', 400, $errors);
}

// Sanitizar dados
$pacienteId = (int)$data['paciente_id'];
$procedimentoId = (int)$data['procedimento_id'];
$dataAgendamento = $data['data_agendamento'];
$horarioAgendamento = isset($data['horario_agendamento']) ? trim($data['horario_agendamento']) : null;
$observacoes = isset($data['observacoes']) ? ApiValidator::sanitizeString($data['observacoes']) : null;

// Validar data de agendamento
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataAgendamento)) {
    ApiResponse::error('Data de agendamento inválida (formato: YYYY-MM-DD)', 400);
}

// Validar horário se fornecido
if ($horarioAgendamento && !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $horarioAgendamento)) {
    ApiResponse::error('Horário de agendamento inválido (formato: HH:MM)', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se o paciente existe
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE id = ?");
    $stmt->execute([$pacienteId]);
    if (!$stmt->fetch()) {
        ApiResponse::error('Paciente não encontrado', 400);
    }

    // Verificar se o procedimento existe
    $stmt = $pdo->prepare("SELECT id FROM valores_procedimentos WHERE id = ?");
    $stmt->execute([$procedimentoId]);
    if (!$stmt->fetch()) {
        ApiResponse::error('Procedimento não encontrado', 400);
    }

    // Gerar código da guia: GE-YYYYMMDD-XXXX
    $codigo = 'GE-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

    // Inserir guia
    $stmt = $pdo->prepare("
        INSERT INTO guias_encaminhamento (
            paciente_id, procedimento_id, data_agendamento, horario_agendamento,
            observacoes, status, data_emissao, codigo
        ) VALUES (
            ?, ?, ?, ?,
            ?, 'agendado', NOW(), ?
        )
    ");

    $stmt->execute([
        $pacienteId,
        $procedimentoId,
        $dataAgendamento,
        $horarioAgendamento,
        $observacoes,
        $codigo
    ]);

    $guideId = $pdo->lastInsertId();

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'criar', 'guias', 'Guia de encaminhamento criada via API mobile', ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $guideId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success([
        'guide_id' => (int)$guideId,
        'codigo' => $codigo
    ], 'Guia criada com sucesso', 201);

} catch (PDOException $e) {
    error_log("Erro ao criar guia: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar guia');
}
