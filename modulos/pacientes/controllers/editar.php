<?php
/**
 * Controlador para o formulário de edição de paciente
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do paciente não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Obtém o ID do paciente
$id = (int) $_GET['id'];

// Instancia o modelo de pacientes
$pacienteModel = new PacienteModel();

// Busca os dados do paciente
$paciente = $pacienteModel->getById($id);

// Verifica se o paciente existe
if (!$paciente) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Paciente não encontrado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=pacientes&action=list');
    exit;
}

// Verifica se há dados de formulário na sessão (em caso de erro)
if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
} else {
    // Formata a data de nascimento para o formato brasileiro
    if (isset($paciente['data_nascimento']) && !empty($paciente['data_nascimento'])) {
        $data = new DateTime($paciente['data_nascimento']);
        $paciente['data_nascimento'] = $data->format('d/m/Y');
    }
    
    $formData = $paciente;
}

// Obtém os erros do formulário, se houver
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Editar Paciente";

// Lista de estados brasileiros para o formulário
$estados = [
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amapá',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Ceará',
    'DF' => 'Distrito Federal',
    'ES' => 'Espírito Santo',
    'GO' => 'Goiás',
    'MA' => 'Maranhão',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Pará',
    'PB' => 'Paraíba',
    'PR' => 'Paraná',
    'PE' => 'Pernambuco',
    'PI' => 'Piauí',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rondônia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'São Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins'
];

// Inclui o template de formulário
include PACIENTES_TEMPLATE_PATH . '/formulario.php';