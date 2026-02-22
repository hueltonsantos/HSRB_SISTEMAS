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
if (!isset($_REQUEST['clinica_id']) || empty($_REQUEST['clinica_id'])) {
    $response['message'] = 'ID da clínica não informado';
    echo json_encode($response);
    exit;
}

// Obtém o ID da clínica
$clinicaId = (int) $_REQUEST['clinica_id'];

try {
    // Carrega a classe de conexão e as configurações se não estiverem carregadas
    require_once 'config.php';
    require_once 'Database.php';
    
    $db = Database::getInstance();
    
    // Consulta para buscar especialidades
    $sql = "SELECT e.id, e.nome 
            FROM especialidades e
            INNER JOIN especialidades_clinicas ec ON e.id = ec.especialidade_id
            WHERE ec.clinica_id = ? AND e.status = 1
            ORDER BY e.nome ASC";
    
    $especialidades = $db->fetchAll($sql, [$clinicaId]);
    
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