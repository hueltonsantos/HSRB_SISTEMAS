<?php
require_once 'auth.php';
verificar_acesso('appointment_view');

header('Content-Type: application/json');

$termo = isset($_POST['termo']) ? $_POST['termo'] : '';

if (strlen($termo) < 3) {
    echo json_encode([]);
    exit;
}

try {
    $pacienteModel = new PacienteModel();
    $pacientes = $pacienteModel->searchPacientes(['nome' => $termo], 10, 0);
    
    // Simplifica o retorno para o JS
    $resultado = [];
    foreach ($pacientes as $paciente) {
        $resultado[] = [
            'id' => $paciente['id'],
            'nome' => $paciente['nome'],
            'cpf' => $paciente['cpf'],
            'celular' => $paciente['celular']
        ];
    }
    
    echo json_encode($resultado);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
