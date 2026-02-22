<?php
/**
 * Controlador para retornar procedimentos por especialidade (novo e simplificado)
 */

// Carrega configurações e Database
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database.php';

try {
    // Verifica se o ID da especialidade foi informado
    if (!isset($_GET['especialidade_id']) || empty($_GET['especialidade_id'])) {
        throw new Exception('ID da especialidade não informado');
    }

    // Obtém o ID da especialidade
    $especialidadeId = (int) $_GET['especialidade_id'];

    // Usa o Database singleton (suporta Docker e localhost)
    $db = Database::getInstance()->getConnection();
    
    // Consulta SQL - usando GROUP BY para evitar duplicações
    $sql = "SELECT id, procedimento, valor_paciente as valor
            FROM valores_procedimentos
            WHERE especialidade_id = ? AND status = 1
            GROUP BY id
            ORDER BY procedimento ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$especialidadeId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Resultado final (sem duplicatas)
    $processedResults = [];
    $processedNames = [];

    foreach ($result as $proc) {
        $key = $proc['procedimento'] . '|' . $proc['valor'];
        if (!in_array($key, $processedNames)) {
            $processedNames[] = $key;
            $processedResults[] = $proc;
        }
    }

    // Retorna o resultado como JSON
    header('Content-Type: application/json');
    echo json_encode($processedResults);
    exit;

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}