<?php
/**
 * Controlador para o formulário de edição de clínica
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da clínica não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Obtém o ID da clínica
$id = (int) $_GET['id'];

// Instancia o modelo de clínicas
$clinicaModel = new ClinicaModel();

// Busca os dados da clínica
$clinica = $clinicaModel->getById($id);

// Verifica se a clínica existe
if (!$clinica) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Clínica não encontrada'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Verifica se há dados de formulário na sessão (em caso de erro)
if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
} else {
    $formData = $clinica;
}

// Obtém os erros do formulário, se houver
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Editar Clínica Parceira";

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
include CLINICAS_TEMPLATE_PATH . '/formulario.php';