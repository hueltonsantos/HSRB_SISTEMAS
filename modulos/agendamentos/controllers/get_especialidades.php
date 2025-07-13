<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Script para obter especialidades de uma clínica via AJAX
 */

// Inicializa a resposta padrão
$response = [
    'success' => false,
    'data' => [],
    'message' => 'Ocorreu um erro ao buscar especialidades'
];

// Verifica se foi informado o ID da clínica
if (!isset($_GET['clinica_id']) || empty($_GET['clinica_id'])) {
    $response['message'] = 'ID da clínica não informado';
    echo json_encode($response);
    exit;
}

// Obtém o ID da clínica
$clinicaId = (int) $_GET['clinica_id'];

// Configurações do banco de dados (ajuste conforme suas configurações)
$host = 'localhost';
$db = 'clinica_encaminhamento'; // Use o nome exato do seu banco
$user = 'root';                  // Ajuste conforme seu usuário
$pass = '';                      // Ajuste conforme sua senha

try {
    // Conexão direta com o banco
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta direta para buscar especialidades
    $sql = "SELECT e.id, e.nome 
            FROM especialidades e
            INNER JOIN especialidades_clinicas ec ON e.id = ec.especialidade_id
            WHERE ec.clinica_id = ? AND e.status = 1
            ORDER BY e.nome ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$clinicaId]);
    $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($especialidades)) {
        $response = [
            'success' => true,
            'data' => [],
            'message' => 'Nenhuma especialidade encontrada para esta clínica'
        ];
    } else {
        $response = [
            'success' => true,
            'data' => $especialidades,
            'message' => 'Especialidades encontradas com sucesso'
        ];
    }
} catch (Exception $e) {
    $response['message'] = 'Erro ao buscar especialidades: ' . $e->getMessage();
}

// Envia a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;