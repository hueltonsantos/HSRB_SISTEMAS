<?php
/**
 * Endpoint para obter configurações do sistema
 * GET /api/settings/get
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

$user = requireAuth();

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("SELECT chave, valor, tipo, descricao FROM configuracoes ORDER BY chave ASC");
    $stmt->execute();
    $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar em objeto chave-valor
    $settings = [];
    foreach ($configs as $config) {
        $settings[$config['chave']] = [
            'valor' => $config['valor'],
            'tipo' => $config['tipo'],
            'descricao' => $config['descricao']
        ];
    }
    
    ApiResponse::success(['settings' => $settings]);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar configurações: " . $e->getMessage());
    ApiResponse::serverError('Erro ao buscar configurações');
}
