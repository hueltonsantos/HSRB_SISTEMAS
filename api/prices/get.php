<?php
/**
 * Endpoint para obter detalhes de um preço
 * GET /api/prices/get?id=1
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();
requirePermission($user, 'price_manage');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    ApiResponse::error('ID do preço não fornecido', 400);
}

$precoId = (int)$_GET['id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT 
            vp.id,
            vp.especialidade_id,
            vp.procedimento,
            vp.valor_paciente,
            vp.valor_repasse,
            vp.status,
            e.nome AS especialidade_nome,
            e.descricao AS especialidade_descricao
        FROM valores_procedimentos vp
        LEFT JOIN especialidades e ON vp.especialidade_id = e.id
        WHERE vp.id = ?
    ");
    $stmt->execute([$precoId]);
    $preco = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$preco) {
        ApiResponse::notFound('Preço não encontrado');
    }
    
    $preco['id'] = (int)$preco['id'];
    $preco['especialidade_id'] = (int)$preco['especialidade_id'];
    $preco['valor_paciente'] = (float)$preco['valor_paciente'];
    $preco['valor_repasse'] = (float)$preco['valor_repasse'];
    $preco['status'] = (int)$preco['status'];
    
    if ($preco['valor_paciente'] > 0) {
        $preco['percentual_repasse'] = round(($preco['valor_repasse'] / $preco['valor_paciente']) * 100, 2);
    } else {
        $preco['percentual_repasse'] = 0;
    }
    
    ApiResponse::success(['price' => $preco]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar preço: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar preço');
}
