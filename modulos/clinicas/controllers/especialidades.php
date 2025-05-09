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

// Definir o título da página
$pageTitle = 'Gerenciar Especialidades da Clínica: ' . $clinica['nome'];
$clinicaId = $id; // Define a variável para uso no template

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

// Filtra as especialidades que ainda não estão vinculadas à clínica
$especialidades = [];
foreach ($todasEspecialidades as $esp) {
    if (!in_array($esp['id'], $especialidadesIds)) {
        $especialidades[] = $esp;
    }
}

// Verifica se foi solicitada a adição de uma especialidade
if (isset($_POST['adicionar_especialidade']) && isset($_POST['especialidade_id'])) {
    $especialidadeId = (int)$_POST['especialidade_id'];
    
    // Verifica se a especialidade existe
    $especialidade = $especialidadeModel->getById($especialidadeId);
    
    if ($especialidade && $especialidade['status'] == 1) {
        // Adiciona a especialidade à clínica
        $result = $clinicaModel->adicionarEspecialidade($id, $especialidadeId);
        
        if ($result) {
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => 'Especialidade adicionada com sucesso!'
            ];
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'danger',
                'texto' => 'Erro ao adicionar especialidade!'
            ];
        }
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Especialidade não encontrada ou inativa!'
        ];
    }
    
    // Redireciona para atualizar a página
    header('Location: index.php?module=clinicas&action=especialidades&id=' . $id);
    exit;
}

// Verifica se foi solicitada a remoção de uma especialidade
if (isset($_GET['remove'])) {
    $especialidadeId = (int)$_GET['remove'];
    
    // Remove a especialidade da clínica
    $result = $clinicaModel->removerEspecialidade($id, $especialidadeId);
    
    if ($result) {
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Especialidade removida com sucesso!'
        ];
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Erro ao remover especialidade!'
        ];
    }
    
    // Redireciona para atualizar a página
    header('Location: index.php?module=clinicas&action=especialidades&id=' . $id);
    exit;
}

// Inclui o template de especialidades
include CLINICAS_TEMPLATE_PATH . '/especialidades.php';