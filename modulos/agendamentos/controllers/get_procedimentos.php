<?php
/**
 * Controlador para retornar procedimentos por especialidade (novo e simplificado)
 */

// Registra para depuração
$log = "Request recebida: " . date('Y-m-d H:i:s') . "\n";
$log .= "GET: " . print_r($_GET, true) . "\n";

try {
    // Verifica se o ID da especialidade foi informado
    if (!isset($_GET['especialidade_id']) || empty($_GET['especialidade_id'])) {
        throw new Exception('ID da especialidade não informado');
    }
    
    // Obtém o ID da especialidade
    $especialidadeId = (int) $_GET['especialidade_id'];
    $log .= "Especialidade ID: $especialidadeId\n";
    
    // Conexão direta ao banco
    $db = new PDO('mysql:host=localhost;dbname=clinica_encaminhamento', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta SQL - usando GROUP BY para evitar duplicações
    $sql = "SELECT id, procedimento, valor 
            FROM valores_procedimentos 
            WHERE especialidade_id = ? AND status = 1 
            GROUP BY id 
            ORDER BY procedimento ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$especialidadeId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $log .= "SQL executada: $sql\n";
    $log .= "Resultados: " . count($result) . " procedimentos encontrados\n";
    $log .= "Dados: " . print_r($result, true) . "\n";
    
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
    
    $log .= "Dados processados: " . count($processedResults) . " procedimentos após remoção de duplicatas\n";
    $log .= "Resultado final: " . print_r($processedResults, true) . "\n";
    
    // Grava o log para depuração
    file_put_contents('debug_procedimentos.txt', $log);
    
    // Retorna o resultado como JSON
    header('Content-Type: application/json');
    echo json_encode($processedResults);
    
} catch (Exception $e) {
    $log .= "ERRO: " . $e->getMessage() . "\n";
    file_put_contents('debug_procedimentos.txt', $log);
    
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}