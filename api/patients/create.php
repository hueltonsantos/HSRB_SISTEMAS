<?php
/**
 * Endpoint para criar um novo paciente
 * POST /api/patients/create
 * 
 * Headers: Authorization: Bearer <token>
 * Body: { "nome": "...", "cpf": "...", ... }
 * Response: { "success": true, "data": { "patient_id": 1 } }
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
$errors = ApiValidator::validateRequired($data, ['nome', 'cpf', 'data_nascimento', 'celular']);
if ($errors) {
    ApiResponse::error('Dados inválidos', 400, $errors);
}

// Sanitizar dados
$nome = ApiValidator::sanitizeString($data['nome']);
$cpf = preg_replace('/\D/', '', $data['cpf']); // Remove caracteres não numéricos
$rg = isset($data['rg']) ? ApiValidator::sanitizeString($data['rg']) : null;
$dataNascimento = $data['data_nascimento'];
$sexo = isset($data['sexo']) ? ApiValidator::sanitizeString($data['sexo']) : null;
$celular = preg_replace('/\D/', '', $data['celular']);
$telefoneFixo = isset($data['telefone_fixo']) ? preg_replace('/\D/', '', $data['telefone_fixo']) : null;
$email = isset($data['email']) ? ApiValidator::sanitizeString($data['email']) : null;
$endereco = isset($data['endereco']) ? ApiValidator::sanitizeString($data['endereco']) : null;
$numero = isset($data['numero']) ? ApiValidator::sanitizeString($data['numero']) : null;
$complemento = isset($data['complemento']) ? ApiValidator::sanitizeString($data['complemento']) : null;
$bairro = isset($data['bairro']) ? ApiValidator::sanitizeString($data['bairro']) : null;
$cidade = isset($data['cidade']) ? ApiValidator::sanitizeString($data['cidade']) : null;
$estado = isset($data['estado']) ? ApiValidator::sanitizeString($data['estado']) : null;
$cep = isset($data['cep']) ? preg_replace('/\D/', '', $data['cep']) : null;
$convenio = isset($data['convenio']) ? ApiValidator::sanitizeString($data['convenio']) : null;
$numeroCarteirinha = isset($data['numero_carteirinha']) ? ApiValidator::sanitizeString($data['numero_carteirinha']) : null;
$observacoes = isset($data['observacoes']) ? ApiValidator::sanitizeString($data['observacoes']) : null;

// Validar CPF (11 dígitos)
if (strlen($cpf) !== 11) {
    ApiResponse::error('CPF inválido', 400);
}

// Validar email se fornecido
if ($email && !ApiValidator::validateEmail($email)) {
    ApiResponse::error('Email inválido', 400);
}

// Validar data de nascimento
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataNascimento)) {
    ApiResponse::error('Data de nascimento inválida (formato: YYYY-MM-DD)', 400);
}

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se CPF já existe
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE cpf = ?");
    $stmt->execute([$cpf]);
    if ($stmt->fetch()) {
        ApiResponse::error('CPF já cadastrado', 400);
    }
    
    // Inserir paciente
    $stmt = $pdo->prepare("
        INSERT INTO pacientes (
            nome, cpf, rg, data_nascimento, sexo, telefone_fixo, celular, email,
            endereco, numero, complemento, bairro, cidade, estado, cep,
            convenio, numero_carteirinha, observacoes,
            status, data_cadastro
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?,
            1, NOW()
        )
    ");

    $stmt->execute([
        $nome, $cpf, $rg, $dataNascimento, $sexo, $telefoneFixo, $celular, $email,
        $endereco, $numero, $complemento, $bairro, $cidade, $estado, $cep,
        $convenio, $numeroCarteirinha, $observacoes
    ]);
    
    $patientId = $pdo->lastInsertId();
    
    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora) 
            VALUES (?, ?, 'criar', 'pacientes', 'Paciente criado via API mobile', ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $patientId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Ignora erro de log
    }
    
    ApiResponse::success([
        'patient_id' => (int)$patientId
    ], 'Paciente criado com sucesso', 201);
    
} catch (PDOException $e) {
    error_log("Erro ao criar paciente: " . $e->getMessage());
    ApiResponse::serverError('Erro ao criar paciente');
}
