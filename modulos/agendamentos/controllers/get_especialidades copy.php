<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);
/**
 * Controlador para obter especialidades de uma clínica via AJAX
 */

// Define o tipo de conteúdo como JSON
header('Content-Type: application/json');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([]);
    exit;
}

// Verifica se o ID da clínica foi informado
if (!isset($_POST['clinica_id']) || empty($_POST['clinica_id'])) {
    echo json_encode([]);
    exit;
}

// Obtém o ID da clínica
$clinicaId = (int) $_POST['clinica_id'];

// Inclui o modelo de clínicas
require_once MODULES_PATH . '/clinicas/models/ClinicaModel.php';
$clinicaModel = new ClinicaModel();

// Busca as especialidades da clínica
$especialidades = $clinicaModel->getEspecialidades($clinicaId);

// Retorna as especialidades em formato JSON
echo json_encode($especialidades);
exit;