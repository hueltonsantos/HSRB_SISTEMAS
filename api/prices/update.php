<?php
/**
 * Endpoint para atualizar preço de procedimento
 * PUT /api/prices/update?id=1
 * Body: { "valor_paciente": 100.00, "valor_repasse": 50.00, "status": 1 }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'price_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do preço não fornecido', 400);
}

$precoId = (int)$_GET['id'];
$data = getJsonInput();

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM valores_procedimentos WHERE id = ?");
    $stmt->execute([$precoId]);
    $precoAntigo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$precoAntigo) {
        ApiResponse::notFound('Preço não encontrado');
    }
    
    $updates = [];
    $updateParams = [];
    
    if (isset($data['valor_paciente'])) {
        $updates[] = 'valor_paciente = ?';
        $updateParams[] = (float)$data['valor_paciente'];
    }
    
    if (isset($data['valor_repasse'])) {
        $updates[] = 'valor_repasse = ?';
        $updateParams[] = (float)$data['valor_repasse'];
    }
    
    if (isset($data['status'])) {
        $updates[] = 'status = ?';
        $updateParams[] = (int)$data['status'];
    }
    
    if (empty($updates)) {
        ApiResponse::error('Nenhum campo para atualizar', 400);
    }
    
    $updateParams[] = $precoId;
    $sql = "UPDATE valores_procedimentos SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateParams);
    
    // Log
    try {
        $stmtLog = $pdo->prepare("
            INSERT INTO logs_sistema 
            (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, dados_novos, ip, user_agent, data_hora) 
            VALUES (?, ?, 'editar', 'tabela_precos', 'Preço atualizado via API mobile', ?, ?, ?, ?, ?, NOW())
        ");
        $stmtLog->execute([
            $user['user_id'],
            $user['email'],
            $precoId,
            json_encode($precoAntigo, JSON_UNESCAPED_UNICODE),
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {}
    
    ApiResponse::success(null, 'Preço atualizado com sucesso');
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar preço: " . $e->getMessage());
    ApiResponse::serverError('Erro ao atualizar preço');
}
