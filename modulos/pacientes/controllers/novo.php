<?php
/**
 * Controlador para o formulário de novo paciente
 */

// Verifica se há dados de formulário na sessão (em caso de erro)
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];

// Limpa os dados da sessão
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Novo Paciente";

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