<?php
/**
 * Controlador para gerenciar procedimentos de uma especialidade
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

// Busca os procedimentos da especialidade
$procedimentos = $especialidadeModel->getValoresProcedimentos($id);

// Formata os valores para exibição
foreach ($procedimentos as &$procedimento) {
    $valor = isset($procedimento['valor_paciente']) ? $procedimento['valor_paciente'] : 0;
    $procedimento['valor_formatado'] = $valorProcedimentoModel->formatDecimalToCurrency($valor);
    
    // Assegura que outras chaves existam para evitar warnings
    $procedimento['valor_paciente'] = $valor;
    $procedimento['valor_repasse'] = isset($procedimento['valor_repasse']) ? $procedimento['valor_repasse'] : 0;
}

// Inclui o template de procedimentos
include ESPECIALIDADES_TEMPLATE_PATH . '/procedimentos.php';