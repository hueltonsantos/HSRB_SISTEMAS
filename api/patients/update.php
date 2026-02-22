<?php
/**
 * Endpoint para atualizar um paciente
 * PUT /api/patients/update
 * 
 * Headers: Authorization: Bearer <token>
 * Body: { "id": 1, "nome": "...", ... }
 * Response: { "success": true, "message": "Paciente atualizado com sucesso" }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas PUT é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_create');

// Obter dados da requisição
$data = getRequestData();

// Validar ID
if (!isset($data['id']) || empty($data['id'])) {
    ApiResponse::error('ID do paciente não fornecido', 400);
}

$patientId = (int)$data['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Verificar se paciente existe
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
    $stmt->execute([$patientId]);
    $pacienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pacienteExistente) {
        ApiResponse::notFound('Paciente não encontrado');
    }
    
    // Preparar campos para atualização
    $updates = [];
    $updateParams = [];
    
    if (isset($data['nome'])) {
        $updates[] = 'nome = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['nome']);
    }
    
    if (isset($data['cpf'])) {
        $cpf = preg_replace('/\D/', '', $data['cpf']);
        if (strlen($cpf) !== 11) {
            ApiResponse::error('CPF inválido', 400);
        }
        
        // Verificar se CPF já existe em outro paciente
        $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE cpf = ? AND id != ?");
        $stmt->execute([$cpf, $patientId]);
        if ($stmt->fetch()) {
            ApiResponse::error('CPF já cadastrado para outro paciente', 400);
        }
        
        $updates[] = 'cpf = ?';
        $updateParams[] = $cpf;
    }
    
    if (isset($data['rg'])) {
        $updates[] = 'rg = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['rg']);
    }
    
    if (isset($data['data_nascimento'])) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['data_nascimento'])) {
            ApiResponse::error('Data de nascimento inválida', 400);
        }
        $updates[] = 'data_nascimento = ?';
        $updateParams[] = $data['data_nascimento'];
    }
    
    if (isset($data['sexo'])) {
        $updates[] = 'sexo = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['sexo']);
    }
    
    if (isset($data['celular'])) {
        $updates[] = 'celular = ?';
        $updateParams[] = preg_replace('/\D/', '', $data['celular']);
    }

    if (isset($data['telefone_fixo'])) {
        $updates[] = 'telefone_fixo = ?';
        $updateParams[] = preg_replace('/\D/', '', $data['telefone_fixo']);
    }
    
    if (isset($data['email'])) {
        if ($data['email'] && !ApiValidator::validateEmail($data['email'])) {
            ApiResponse::error('Email inválido', 400);
        }
        $updates[] = 'email = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['email']);
    }
    
    if (isset($data['endereco'])) {
        $updates[] = 'endereco = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['endereco']);
    }
    
    if (isset($data['numero'])) {
        $updates[] = 'numero = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['numero']);
    }
    
    if (isset($data['complemento'])) {
        $updates[] = 'complemento = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['complemento']);
    }
    
    if (isset($data['bairro'])) {
        $updates[] = 'bairro = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['bairro']);
    }
    
    if (isset($data['cidade'])) {
        $updates[] = 'cidade = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['cidade']);
    }
    
    if (isset($data['estado'])) {
        $updates[] = 'estado = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['estado']);
    }
    
    if (isset($data['cep'])) {
        $updates[] = 'cep = ?';
        $updateParams[] = preg_replace('/\D/', '', $data['cep']);
    }
    
    if (isset($data['convenio'])) {
        $updates[] = 'convenio = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['convenio']);
    }

    if (isset($data['numero_carteirinha'])) {
        $updates[] = 'numero_carteirinha = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['numero_carteirinha']);
    }

    if (isset($data['observacoes'])) {
        $updates[] = 'observacoes = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['observacoes']);
    }
    
    if (isset($data['status'])) {
        $updates[] = 'status = ?';
        $updateParams[] = (int)$data['status'];
    }
    
    if (empty($updates)) {
        ApiResponse::error('Nenhum campo para atualizar', 400);
    }
    
    // Adicionar data de atualização
    $updates[] = 'ultima_atualizacao = NOW()';
    
    // Executar atualização
    $updateParams[] = $patientId;
    $sql = "UPDATE pacientes SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);
    
    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora) 
            VALUES (?, ?, 'editar', 'pacientes', 'Paciente atualizado via API mobile', ?, ?, ?, NOW())
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
    
    ApiResponse::success(null, 'Paciente atualizado com sucesso');
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar paciente: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar paciente');
}
