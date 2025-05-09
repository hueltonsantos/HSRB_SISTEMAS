<?php
/**
 * Controlador para visualização de especialidade
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da especialidade não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID da especialidade
$id = (int) $_GET['id'];

// Instancia os modelos
$especialidadeModel = new EspecialidadeModel();
$valorProcedimentoModel = new ValorProcedimentoModel();

// Busca os dados da especialidade
$especialidade = $especialidadeModel->getById($id);

// Verifica se a especialidade existe
if (!$especialidade) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Especialidade não encontrada'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Formata a data de cadastro para exibição
if (isset($especialidade['data_cadastro']) && !empty($especialidade['data_cadastro'])) {
    $especialidade['data_cadastro_formatada'] = $especialidadeModel->formatDateForDisplay($especialidade['data_cadastro'], true);
}

// Define o status como texto
$especialidade['status_texto'] = $especialidade['status'] == 1 ? 'Ativa' : 'Inativa';

// Busca os procedimentos da especialidade
$procedimentos = $especialidadeModel->getValoresProcedimentos($id);

// Formata os valores para exibição
foreach ($procedimentos as &$procedimento) {
    $procedimento['valor_formatado'] = $valorProcedimentoModel->formatDecimalToCurrency($procedimento['valor']);
}

// Inclui o template de visualização
include ESPECIALIDADES_TEMPLATE_PATH . '/visualizar.php';