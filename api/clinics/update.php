<?php
/**
 * Endpoint para atualizar uma clínica
 * PUT /api/clinics/update
 *
 * Headers: Authorization: Bearer <token>
 * Body: { "id": 1, "nome": "...", ... }
 * Response: { "success": true, "message": "Clínica atualizada com sucesso" }
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
    ApiResponse::error('ID da clínica não fornecido', 400);
}

$clinicaId = (int)$data['id'];

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Verificar se clínica existe
    $stmt = $pdo->prepare("SELECT * FROM clinicas_parceiras WHERE id = ?");
    $stmt->execute([$clinicaId]);
    $clinicaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clinicaExistente) {
        ApiResponse::notFound('Clínica não encontrada');
    }

    // Preparar campos para atualização
    $updates = [];
    $updateParams = [];

    if (isset($data['nome'])) {
        $updates[] = 'nome = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['nome']);
    }

    if (isset($data['razao_social'])) {
        $updates[] = 'razao_social = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['razao_social']);
    }

    if (isset($data['cnpj'])) {
        $cnpj = preg_replace('/\D/', '', $data['cnpj']);
        if (strlen($cnpj) !== 14) {
            ApiResponse::error('CNPJ inválido', 400);
        }

        // Verificar se CNPJ já existe em outra clínica
        $stmt = $pdo->prepare("SELECT id FROM clinicas_parceiras WHERE cnpj = ? AND id != ?");
        $stmt->execute([$cnpj, $clinicaId]);
        if ($stmt->fetch()) {
            ApiResponse::error('CNPJ já cadastrado para outra clínica', 400);
        }

        $updates[] = 'cnpj = ?';
        $updateParams[] = $cnpj;
    }

    if (isset($data['responsavel'])) {
        $updates[] = 'responsavel = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['responsavel']);
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

    if (isset($data['telefone'])) {
        $updates[] = 'telefone = ?';
        $updateParams[] = preg_replace('/\D/', '', $data['telefone']);
    }

    if (isset($data['celular'])) {
        $updates[] = 'celular = ?';
        $updateParams[] = preg_replace('/\D/', '', $data['celular']);
    }

    if (isset($data['email'])) {
        if ($data['email'] && !ApiValidator::validateEmail($data['email'])) {
            ApiResponse::error('Email inválido', 400);
        }
        $updates[] = 'email = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['email']);
    }

    if (isset($data['site'])) {
        $updates[] = 'site = ?';
        $updateParams[] = ApiValidator::sanitizeString($data['site']);
    }

    if (isset($data['tipo'])) {
        $tipo = ApiValidator::sanitizeString($data['tipo']);
        if (!in_array($tipo, ['master', 'parceira'])) {
            ApiResponse::error('Tipo inválido (valores aceitos: master, parceira)', 400);
        }
        $updates[] = 'tipo = ?';
        $updateParams[] = $tipo;
    }

    if (isset($data['percentual_repasse'])) {
        $updates[] = 'percentual_repasse = ?';
        $updateParams[] = (float)$data['percentual_repasse'];
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
    $updateParams[] = $clinicaId;
    $sql = "UPDATE clinicas_parceiras SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);

    // Registrar log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, ip, user_agent, data_hora)
            VALUES (?, ?, 'editar', 'clinicas', 'Clínica atualizada via API mobile', ?, ?, ?, NOW())
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

    ApiResponse::success(null, 'Clínica atualizada com sucesso');

} catch (PDOException $e) {
    error_log("Erro ao atualizar clínica: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar clínica');
}
