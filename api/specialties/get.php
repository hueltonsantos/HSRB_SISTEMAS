<?php
/**
 * Endpoint para obter detalhes de uma especialidade
 * GET /api/specialties/get?id=1
 *
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "specialty": {...}, "procedimentos": [...] } }
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
    ApiResponse::error('ID da especialidade não fornecido', 400);
}

$specialtyId = (int)$_GET['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Buscar especialidade
    $stmt = $pdo->prepare("
        SELECT
            id,
            nome,
            descricao,
            status
        FROM especialidades
        WHERE id = ?
    ");
    $stmt->execute([$specialtyId]);
    $especialidade = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$especialidade) {
        ApiResponse::notFound('Especialidade não encontrada');
    }

    // Formatar dados
    $especialidade['id'] = (int)$especialidade['id'];
    $especialidade['status'] = (int)$especialidade['status'];

    // Buscar procedimentos ativos vinculados a esta especialidade
    $stmtProc = $pdo->prepare("
        SELECT
            id,
            procedimento,
            valor_paciente,
            valor_repasse,
            status
        FROM valores_procedimentos
        WHERE especialidade_id = ? AND status = 1
        ORDER BY procedimento ASC
    ");
    $stmtProc->execute([$specialtyId]);
    $procedimentos = $stmtProc->fetchAll(PDO::FETCH_ASSOC);

    // Formatar dados dos procedimentos
    foreach ($procedimentos as &$proc) {
        $proc['id'] = (int)$proc['id'];
        $proc['valor_paciente'] = (float)$proc['valor_paciente'];
        $proc['valor_repasse'] = (float)$proc['valor_repasse'];
        $proc['status'] = (int)$proc['status'];
    }

    $especialidade['procedimentos'] = $procedimentos;

    ApiResponse::success(['specialty' => $especialidade]);

} catch (PDOException $e) {
    error_log("Erro ao buscar especialidade: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar especialidade');
}
