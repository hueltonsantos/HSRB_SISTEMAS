<?php
/**
 * Endpoint para obter detalhes de uma guia de encaminhamento
 * GET /api/guides/get?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "guide": {...} } }
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
    ApiResponse::error('ID da guia não fornecido', 400);
}

$guideId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Buscar guia com JOINs completos
    $stmt = $pdo->prepare("
        SELECT
            g.id,
            g.codigo,
            g.paciente_id,
            g.procedimento_id,
            g.data_agendamento,
            g.horario_agendamento,
            g.observacoes,
            g.status,
            g.data_emissao,
            p.nome AS paciente_nome,
            p.cpf AS paciente_cpf,
            p.data_nascimento AS paciente_data_nascimento,
            p.celular AS paciente_telefone,
            p.email AS paciente_email,
            p.endereco AS paciente_endereco,
            p.cidade AS paciente_cidade,
            p.estado AS paciente_estado,
            vp.procedimento AS procedimento_nome,
            vp.valor_paciente AS procedimento_valor,
            e.id AS especialidade_id,
            e.nome AS especialidade_nome
        FROM guias_encaminhamento g
        LEFT JOIN pacientes p ON g.paciente_id = p.id
        LEFT JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
        LEFT JOIN especialidades e ON vp.especialidade_id = e.id
        WHERE g.id = ?
    ");
    $stmt->execute([$guideId]);
    $guia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guia) {
        ApiResponse::notFound('Guia não encontrada');
    }

    // Formatar dados
    $guia['id'] = (int)$guia['id'];
    $guia['paciente_id'] = (int)$guia['paciente_id'];
    $guia['procedimento_id'] = (int)$guia['procedimento_id'];
    $guia['procedimento_valor'] = $guia['procedimento_valor'] !== null ? (float)$guia['procedimento_valor'] : null;
    $guia['especialidade_id'] = $guia['especialidade_id'] !== null ? (int)$guia['especialidade_id'] : null;

    // Calcular idade do paciente
    if ($guia['paciente_data_nascimento']) {
        $nascimento = new DateTime($guia['paciente_data_nascimento']);
        $hoje = new DateTime();
        $guia['paciente_idade'] = $hoje->diff($nascimento)->y;
    }

    // Formatar CPF (XXX.XXX.XXX-XX)
    if ($guia['paciente_cpf']) {
        $cpf = preg_replace('/\D/', '', $guia['paciente_cpf']);
        if (strlen($cpf) === 11) {
            $guia['paciente_cpf_formatado'] = substr($cpf, 0, 3) . '.' .
                                               substr($cpf, 3, 3) . '.' .
                                               substr($cpf, 6, 3) . '-' .
                                               substr($cpf, 9, 2);
        }
    }

    ApiResponse::success(['guide' => $guia]);

} catch (PDOException $e) {
    error_log("Erro ao buscar guia: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar guia');
}
