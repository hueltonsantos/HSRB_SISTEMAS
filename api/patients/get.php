<?php
/**
 * Endpoint para obter detalhes de um paciente
 * GET /api/patients/get?id=1
 * 
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "patient": {...} } }
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
    ApiResponse::error('ID do paciente não fornecido', 400);
}

$patientId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar paciente
    $stmt = $pdo->prepare("
        SELECT
            id,
            nome,
            cpf,
            rg,
            data_nascimento,
            sexo,
            telefone_fixo,
            celular,
            email,
            endereco,
            numero,
            complemento,
            bairro,
            cidade,
            estado,
            cep,
            convenio,
            numero_carteirinha,
            observacoes,
            status,
            data_cadastro,
            ultima_atualizacao
        FROM pacientes
        WHERE id = ?
    ");
    $stmt->execute([$patientId]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$paciente) {
        ApiResponse::notFound('Paciente não encontrado');
    }
    
    // Formatar dados
    $paciente['id'] = (int)$paciente['id'];
    $paciente['status'] = (int)$paciente['status'];
    
    // Calcular idade
    if ($paciente['data_nascimento']) {
        $nascimento = new DateTime($paciente['data_nascimento']);
        $hoje = new DateTime();
        $paciente['idade'] = $hoje->diff($nascimento)->y;
    }
    
    // Buscar histórico de agendamentos do paciente
    $stmtAgendamentos = $pdo->prepare("
        SELECT
            a.id,
            a.data_consulta AS data_agendamento,
            a.hora_consulta AS hora_agendamento,
            a.status_agendamento AS status,
            e.nome as especialidade_nome,
            cp.nome as clinica_nome
        FROM agendamentos a
        LEFT JOIN especialidades e ON a.especialidade_id = e.id
        LEFT JOIN clinicas_parceiras cp ON a.clinica_id = cp.id
        WHERE a.paciente_id = ?
        ORDER BY a.data_consulta DESC, a.hora_consulta DESC
        LIMIT 10
    ");
    $stmtAgendamentos->execute([$patientId]);
    $agendamentos = $stmtAgendamentos->fetchAll(PDO::FETCH_ASSOC);
    
    $paciente['agendamentos_recentes'] = $agendamentos;
    
    ApiResponse::success(['patient' => $paciente]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar paciente: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar paciente');
}
