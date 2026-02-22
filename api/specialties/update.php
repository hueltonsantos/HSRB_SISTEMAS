<?php
/**
 * Endpoint para atualizar uma especialidade
 * PUT /api/specialties/update
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "id": 1, "nome": "...", "descricao": "...", "status": 1 }
 * Response: { "success": true, "message": "Especialidade atualizada com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas PUT é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'role_manage');

// Obter dados da requisição
$data = getRequestData();

// Validar ID
if (!isset($data['id']) || empty($data['id'])) {
    ApiResponse::error('ID da especialidade não fornecido', 400);
}

$specialtyId = (int)$data['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se especialidade existe
    $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id = ?");
    $stmt->execute([$specialtyId]);
    $especialidadeExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$especialidadeExistente) {
        ApiResponse::notFound('Especialidade não encontrada');
    }

    // Preparar campos para atualização
    $updates = [];
    $updateParams = [];

    if (isset($data['nome'])) {
        $nome = ApiValidator::sanitizeString($data['nome']);

        if (empty($nome)) {
            ApiResponse::error('O nome da especialidade não pode ser vazio', 400);
        }

        // Verificar se já existe outra especialidade com o mesmo nome
        $stmt = $pdo->prepare("SELECT id FROM especialidades WHERE nome = ? AND id != ?");
        $stmt->execute([$nome, $specialtyId]);
        if ($stmt->fetch()) {
            ApiResponse::error('Já existe outra especialidade com este nome', 400);
        }

        $updates[] = 'nome = ?';
        $updateParams[] = $nome;
    }

    if (isset($data['descricao'])) {
        $updates[] = 'descricao = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['descricao']);
    }

    if (isset($data['status'])) {
        $updates[] = 'status = ?';
        $updateParams[] = (int)$data['status'];
    }

    if (empty($updates)) {
        ApiResponse::error('Nenhum campo para atualizar', 400);
    }

    // Executar atualização
    $updateParams[] = $specialtyId;
    $sql = "UPDATE especialidades SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'editar', 'especialidades', 'Especialidade atualizada via API mobile', ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $specialtyId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success(null, 'Especialidade atualizada com sucesso');

} catch (PDOException $e) {
    error_log("Erro ao atualizar especialidade: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar especialidade');
}
