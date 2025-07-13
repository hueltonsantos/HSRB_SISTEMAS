<?php
/**
 * Controlador para salvar especialidades da clínica
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Verifica se o ID foi informado
if (!isset($_POST['clinica_id']) || empty($_POST['clinica_id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da clínica não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Obtém o ID da clínica
$id = (int) $_POST['clinica_id'];

// Instancia o modelo de clínicas
$clinicaModel = new ClinicaModel();

// Verifica se a clínica existe
$clinica = $clinicaModel->getById($id);
if (!$clinica) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Clínica não encontrada'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=clinicas&action=list');
    exit;
}

// Obtém as especialidades selecionadas
$especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];

// Salva as especialidades
$result = $clinicaModel->saveEspecialidades($id, $especialidades);

if ($result) {
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Especialidades da clínica atualizadas com sucesso!'
    ];
} else {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao atualizar especialidades da clínica.'
    ];
}

// Redireciona para a visualização da clínica
header('Location: index.php?module=clinicas&action=view&id=' . $id);
exit;