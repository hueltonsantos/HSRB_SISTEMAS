<?php
/**
 * Endpoint para criar uma nova especialidade
 * POST /api/specialties/create
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "nome": "...", "descricao": "..." }
 * Response: { "success": true, "data": { "specialty_id": 1 } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'role_manage');

// Obter dados da requisição
$data = getRequestData();

// Validar campos obrigatórios
$errors = ApiValidator::validateRequired($data, ['nome']);
if ($errors) {
    ApiResponse::error('Dados inválidos', 400, $errors);
}

// Sanitizar dados
$nome = ApiValidator::sanitizeString($data['nome']);
$descricao = isset($data['descricao']) ? ApiValidator::sanitizeString($data['descricao']) : null;

// Validar nome não vazio após sanitização
if (empty($nome)) {
    ApiResponse::error('O nome da especialidade não pode ser vazio', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se já existe especialidade com o mesmo nome
    $stmt = $pdo->prepare("SELECT id FROM especialidades WHERE nome = ?");
    $stmt->execute([$nome]);
    if ($stmt->fetch()) {
        ApiResponse::error('Já existe uma especialidade com este nome', 400);
    }

    // Inserir especialidade
    $stmt = $pdo->prepare("
        INSERT INTO especialidades (nome, descricao, status)
        VALUES (?, ?, 1)
    ");
    $stmt->execute([$nome, $descricao]);

    $specialtyId = $pdo->lastInsertId();

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'criar', 'especialidades', 'Especialidade criada via API mobile', ?, ?, ?, NOW())
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

    ApiResponse::success([
        'specialty_id' => (int)$specialtyId
    ], 'Especialidade criada com sucesso', 201);

} catch (PDOException $e) {
    error_log("Erro ao criar especialidade: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar especialidade');
}
