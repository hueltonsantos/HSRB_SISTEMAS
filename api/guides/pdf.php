<?php
/**
 * Endpoint para obter dados completos de uma guia para geração de PDF
 * GET /api/guides/pdf?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "guide": {...}, "clinic": {...} } }
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

    // Buscar guia com JOINs completos para PDF
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

    // Formatar dados da guia
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

    // Buscar configurações da clínica
    $stmtConfig = $pdo->prepare("
        SELECT chave, valor
        FROM configuracoes
        WHERE chave IN ('nome_clinica', 'endereco_clinica', 'telefone_clinica', 'email_clinica')
    ");
    $stmtConfig->execute();
    $configRows = $stmtConfig->fetchAll(PDO::FETCH_ASSOC);

    $clinic = [];
    foreach ($configRows as $row) {
        $clinic[$row['chave']] = $row['valor'];
    }

    // Registrar log de acesso ao PDF
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'visualizar', 'guias', 'Dados de guia para PDF acessados via API mobile', ?, ?, ?, NOW())
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
        'guide' => $guia,
        'clinic' => $clinic
    ]);

} catch (PDOException $e) {
    error_log("Erro ao buscar dados da guia para PDF: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar dados da guia');
}
