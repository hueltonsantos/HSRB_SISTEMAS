<?php
/**
 * Endpoint para criar um novo agendamento
 * POST /api/appointments/create
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "paciente_id": 1, "clinica_id": 1, "especialidade_id": 1, "data_consulta": "2026-02-15", "hora_consulta": "14:30", "procedimento_id": null, "observacoes": "" }
 * Response: { "success": true, "data": { "appointment_id": 1 } }
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
$errors = ApiValidator::validateRequired($data, ['paciente_id', 'clinica_id', 'especialidade_id', 'data_consulta', 'hora_consulta']);
if ($errors) {
    ApiResponse::error('Dados inválidos', 400, $errors);
}

// Sanitizar e validar dados
$pacienteId = (int)$data['paciente_id'];
$especialidadeId = (int)$data['especialidade_id'];
$procedimentoId = isset($data['procedimento_id']) && $data['procedimento_id'] ? (int)$data['procedimento_id'] : null;
$dataConsulta = trim($data['data_consulta']);
$horaConsulta = trim($data['hora_consulta']);
$observacoes = isset($data['observacoes']) ? ApiValidator::sanitizeString($data['observacoes']) : null;

// Definir clínica_id (priorizar clínica do usuário se existir)
$clinicaId = $user['clinica_id'] ? (int)$user['clinica_id'] : (int)$data['clinica_id'];

// Validar formato da data (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataConsulta)) {
    ApiResponse::error('Data da consulta inválida (formato: YYYY-MM-DD)', 400);
}

// Validar formato da hora (HH:MM ou HH:MM:SS)
if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $horaConsulta)) {
    ApiResponse::error('Hora da consulta inválida (formato: HH:MM)', 400);
}

// Garantir formato HH:MM:SS para o banco
if (preg_match('/^\d{2}:\d{2}$/', $horaConsulta)) {
    $horaConsulta .= ':00';
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se paciente existe
    $stmt = $pdo->prepare("SELECT id, nome FROM pacientes WHERE id = ?");
    $stmt->execute([$pacienteId]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$paciente) {
        ApiResponse::error('Paciente não encontrado', 400);
    }

    // Verificar se clínica existe
    $stmt = $pdo->prepare("SELECT id FROM clinicas_parceiras WHERE id = ?");
    $stmt->execute([$clinicaId]);
    if (!$stmt->fetch()) {
        ApiResponse::error('Clínica não encontrada', 400);
    }

    // Verificar se especialidade existe
    $stmt = $pdo->prepare("SELECT id, nome FROM especialidades WHERE id = ?");
    $stmt->execute([$especialidadeId]);
    $especialidade = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$especialidade) {
        ApiResponse::error('Especialidade não encontrada', 400);
    }

    // Verificar procedimento se fornecido
    if ($procedimentoId) {
        $stmt = $pdo->prepare("SELECT id FROM valores_procedimentos WHERE id = ?");
        $stmt->execute([$procedimentoId]);
        if (!$stmt->fetch()) {
            ApiResponse::error('Procedimento não encontrado', 400);
        }
    }

    // Inserir agendamento
    $stmt = $pdo->prepare("
        INSERT INTO agendamentos (
            paciente_id, clinica_id, especialidade_id, procedimento_id,
            data_consulta, hora_consulta, status_agendamento, observacoes,
            data_agendamento, ultima_atualizacao
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, 'agendado', ?,
            NOW(), NOW()
        )
    ");

    $stmt->execute([
        $pacienteId, $clinicaId, $especialidadeId, $procedimentoId,
        $dataConsulta, $horaConsulta, $observacoes
    ]);

    $appointmentId = $pdo->lastInsertId();

    // Inserir notificação
    try {
        $stmtNotif = $pdo->prepare("
            INSERT INTO notificacoes
            (tipo, icone, cor, titulo, mensagem, link, lida, usuario_id, data_criacao)
            VALUES ('agendamento', 'fa-calendar-check', 'success', ?, ?, ?, 0, ?, NOW())
        ");
        $titulo = 'Novo Agendamento';
        $mensagem = 'Agendamento criado para ' . $paciente['nome'] . ' - ' . $especialidade['nome'] . ' em ' . date('d/m/Y', strtotime($dataConsulta));
        $link = '?page=agendamentos&action=editar&id=' . $appointmentId;
        $stmtNotif->execute([
            $titulo,
            $mensagem,
            $link,
            $user['user_id']
        ]);
    } catch (Exception $e) {
        // Ignora erro de notificação
    }

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'criar', 'agendamentos', 'Agendamento criado via API mobile', ?, ?, ?, NOW())
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

    ApiResponse::success([
        'appointment_id' => (int)$appointmentId
    ], 'Agendamento criado com sucesso', 201);

} catch (PDOException $e) {
    error_log("Erro ao criar agendamento: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar agendamento');
}
