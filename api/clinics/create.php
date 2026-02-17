<?php
/**
 * Endpoint para criar uma nova clínica
 * POST /api/clinics/create
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "nome": "...", "endereco": "...", "cidade": "...", "estado": "...", "telefone": "...", ... }
 * Response: { "success": true, "data": { "clinic_id": 1 } }
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
$errors = ApiValidator::validateRequired($data, ['nome', 'endereco', 'cidade', 'estado', 'telefone']);
if ($errors) {
    ApiResponse::error('Dados inválidos', 400, $errors);
}

// Sanitizar dados obrigatórios
$nome = ApiValidator::sanitizeString($data['nome']);
$endereco = ApiValidator::sanitizeString($data['endereco']);
$cidade = ApiValidator::sanitizeString($data['cidade']);
$estado = ApiValidator::sanitizeString($data['estado']);
$telefone = preg_replace('/\D/', '', $data['telefone']);

// Sanitizar dados opcionais
$razaoSocial = isset($data['razao_social']) ? ApiValidator::sanitizeString($data['razao_social']) : null;
$cnpj = isset($data['cnpj']) ? preg_replace('/\D/', '', $data['cnpj']) : null;
$responsavel = isset($data['responsavel']) ? ApiValidator::sanitizeString($data['responsavel']) : null;
$numero = isset($data['numero']) ? ApiValidator::sanitizeString($data['numero']) : null;
$complemento = isset($data['complemento']) ? ApiValidator::sanitizeString($data['complemento']) : null;
$bairro = isset($data['bairro']) ? ApiValidator::sanitizeString($data['bairro']) : null;
$cep = isset($data['cep']) ? preg_replace('/\D/', '', $data['cep']) : null;
$celular = isset($data['celular']) ? preg_replace('/\D/', '', $data['celular']) : null;
$email = isset($data['email']) ? ApiValidator::sanitizeString($data['email']) : null;
$site = isset($data['site']) ? ApiValidator::sanitizeString($data['site']) : null;

// Validar CNPJ se fornecido (14 dígitos)
if ($cnpj && strlen($cnpj) !== 14) {
    ApiResponse::error('CNPJ inválido', 400);
}

// Validar email se fornecido
if ($email && !ApiValidator::validateEmail($email)) {
    ApiResponse::error('Email inválido', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se CNPJ já existe (se fornecido)
    if ($cnpj) {
        $stmt = $pdo->prepare("SELECT id FROM clinicas_parceiras WHERE cnpj = ?");
        $stmt->execute([$cnpj]);
        if ($stmt->fetch()) {
            ApiResponse::error('CNPJ já cadastrado', 400);
        }
    }

    // Inserir clínica
    $stmt = $pdo->prepare("
        INSERT INTO clinicas_parceiras (
            nome, razao_social, cnpj, responsavel,
            endereco, numero, complemento, bairro, cidade, estado, cep,
            telefone, celular, email, site,
            status, data_cadastro
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            1, NOW()
        )
    ");

    $stmt->execute([
        $nome, $razaoSocial, $cnpj, $responsavel,
        $endereco, $numero, $complemento, $bairro, $cidade, $estado, $cep,
        $telefone, $celular, $email, $site
    ]);

    $clinicaId = $pdo->lastInsertId();

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'criar', 'clinicas', 'Clínica criada via API mobile', ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $clinicaId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }

    ApiResponse::success([
        'clinic_id' => (int)$clinicaId
    ], 'Clínica criada com sucesso', 201);

} catch (PDOException $e) {
    error_log("Erro ao criar clínica: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar clínica');
}
