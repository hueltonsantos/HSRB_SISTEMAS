<?php
/**
 * Controller para Imprimir Evolução
 */

if (!isset($_GET['id'])) {
    die("ID da evolução não informado.");
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (function_exists('hasPermission') && !hasPermission('painel_profissional') && !hasPermission('minha_clinica_pacientes')) {
    die('Acesso negado');
}

require_once MINHA_CLINICA_PATH . '/models/ProntuarioModel.php';
$model = new ProntuarioModel();
$evolucao = $model->getEvolucao($_GET['id']);

if (!$evolucao) {
    die("Evolução não encontrada.");
}

// Verifica permissão (se necessário)
// Pode adicionar verificação se o usuário tem acesso a este paciente

require_once MINHA_CLINICA_TEMPLATES_PATH . '/prontuario/imprimir_evolucao.php';
