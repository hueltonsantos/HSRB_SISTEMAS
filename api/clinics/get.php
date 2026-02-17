<?php
/**
 * Endpoint para obter detalhes de uma clínica
 * GET /api/clinics/get?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "clinic": {..., "especialidades": [...]} } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'role_manage');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID da clínica não fornecido', 400);
}

$clinicaId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Buscar clínica
    $stmt = $pdo->prepare("
        SELECT
            id,
            nome,
            razao_social,
            cnpj,
            responsavel,
            endereco,
            numero,
            complemento,
            bairro,
            cidade,
            estado,
            cep,
            telefone,
            celular,
            email,
            site,
            tipo,
            percentual_repasse,
            status,
            data_cadastro,
            ultima_atualizacao
        FROM clinicas_parceiras
        WHERE id = ?
    ");
    $stmt->execute([$clinicaId]);
    $clinica = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clinica) {
        ApiResponse::notFound('Clínica não encontrada');
    }

    // Formatar dados
    $clinica['id'] = (int)$clinica['id'];
    $clinica['status'] = (int)$clinica['status'];
    $clinica['percentual_repasse'] = $clinica['percentual_repasse'] !== null ? (float)$clinica['percentual_repasse'] : null;

    // Formatar CNPJ (XX.XXX.XXX/XXXX-XX)
    if ($clinica['cnpj']) {
        $cnpj = preg_replace('/\D/', '', $clinica['cnpj']);
        if (strlen($cnpj) === 14) {
            $clinica['cnpj_formatado'] = substr($cnpj, 0, 2) . '.' .
                                         substr($cnpj, 2, 3) . '.' .
                                         substr($cnpj, 5, 3) . '/' .
                                         substr($cnpj, 8, 4) . '-' .
                                         substr($cnpj, 12, 2);
        }
    }

    // Buscar especialidades vinculadas à clínica
    $stmtEspecialidades = $pdo->prepare("
        SELECT
            e.id,
            e.nome,
            ec.observacoes,
            ec.status
        FROM especialidades_clinicas ec
        INNER JOIN especialidades e ON ec.especialidade_id = e.id
        WHERE ec.clinica_id = ?
        ORDER BY e.nome ASC
    ");
    $stmtEspecialidades->execute([$clinicaId]);
    $especialidades = $stmtEspecialidades->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados das especialidades
    foreach ($especialidades as &$especialidade) {
        $especialidade['id'] = (int)$especialidade['id'];
        $especialidade['status'] = (int)$especialidade['status'];
    }

    $clinica['especialidades'] = $especialidades;

    ApiResponse::success(['clinic' => $clinica]);

} catch (PDOException $e) {
    error_log("Erro ao buscar clínica: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar clínica');
}
