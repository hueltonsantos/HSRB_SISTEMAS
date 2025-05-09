<?php
/**
 * Controlador para gerenciar especialidades da clínica
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

// Inclui o modelo de especialidades (do módulo de especialidades)
require_once MODULES_PATH . '/especialidades/models/EspecialidadeModel.php';
$especialidadeModel = new EspecialidadeModel();

// Busca todas as especialidades disponíveis
$todasEspecialidades = $especialidadeModel->getAll(['status' => 1], 'nome');

// Busca as especialidades que a clínica já possui
$especialidadesClinica = $clinicaModel->getEspecialidades($id);
$especialidadesIds = [];

foreach ($especialidadesClinica as $esp) {
    $especialidadesIds[] = $esp['id'];
}

// Inclui o template de especialidades
include CLINICAS_TEMPLATE_PATH . '/especialidades.php';