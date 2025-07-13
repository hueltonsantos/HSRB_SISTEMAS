<?php
/**
 * Controlador para o formulário de nova especialidade
 */

// Verifica se há dados de formulário na sessão (em caso de erro)
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$formErrors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];

// Limpa os dados da sessão
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Define o título da página
$pageTitle = "Nova Especialidade";

// Inclui o template de formulário
include ESPECIALIDADES_TEMPLATE_PATH . '/formulario.php';