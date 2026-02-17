<?php

/**
 * Controlador para reimprimir uma guia
 */

// Verifica se o ID foi informado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da guia não informado'
    ];

    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}

// Define paths if needed (assuming moduless/configuracoes follows strict structure)
if (!defined('CONFIGURACOES_MODEL_PATH')) {
    define('CONFIGURACOES_MODEL_PATH', __DIR__ . '/../../configuracoes/models/');
}

// Obtém o ID da guia
$guiaId = (int) $_GET['id'];

try {
    // Conecta ao banco de dados usando a classe Database do sistema
    $db = Database::getInstance()->getConnection();

    // Busca informações da guia
    // $stmt = $db->prepare("
    //     SELECT g.*, p.nome as paciente_nome, p.documento as paciente_documento,
    //            vp.procedimento as procedimento_nome, vp.valor as procedimento_valor,
    //            e.nome as especialidade_nome, cp.nome as clinica_nome,
    //            cp.endereco, cp.telefone, cp.observacoes as clinica_observacoes,
    //            e.id as especialidade_id
    //     FROM guias_encaminhamento g
    //     INNER JOIN pacientes p ON g.paciente_id = p.id
    //     INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
    //     INNER JOIN especialidades e ON vp.especialidade_id = e.id
    //     LEFT JOIN clinicas_parceiras cp ON e.id = cp.especialidade_id
    //     WHERE g.id = ?
    //     LIMIT 1
    // ");


    // Modifique a consulta SQL para:
    // Carrega configurações
    require_once CONFIGURACOES_MODEL_PATH . 'ConfiguracaoModel.php';
    $configModel = new ConfiguracaoModel();
    $configs = [];
    foreach($configModel->listar() as $c) {
        $configs[$c['chave']] = $c['valor'];
    }

    // Busca informações da guia
    $stmt = $db->prepare("
        SELECT g.*, p.nome as paciente_nome, p.cpf as paciente_documento,
               vp.procedimento as procedimento_nome, vp.valor_paciente as procedimento_valor,
               e.nome as especialidade_nome, cp.nome as clinica_nome,
               cp.endereco, cp.telefone
        FROM guias_encaminhamento g
        INNER JOIN pacientes p ON g.paciente_id = p.id
        INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
        INNER JOIN especialidades e ON vp.especialidade_id = e.id
        LEFT JOIN clinicas_parceiras cp ON e.id = cp.id
        WHERE g.id = ?
        LIMIT 1
    ");
    $stmt->execute([$guiaId]);
    $guia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guia) {
        throw new Exception("Guia não encontrada");
    }

    // Formata os dados para o template de impressão
    $guiaData = [
        'id' => $guia['id'],
        'codigo' => $guia['codigo'],
        'procedimento' => [
            'id' => $guia['procedimento_id'],
            'procedimento' => $guia['procedimento_nome'],
            'valor' => $guia['procedimento_valor'],
            // 'especialidade_id' => $guia['especialidade_id'],
            'especialidade_nome' => $guia['especialidade_nome'],
            'clinica_nome' => $guia['clinica_nome'],
            'endereco' => $guia['endereco'],
            'telefone' => $guia['telefone'],
            // 'clinica_observacoes' => $guia['clinica_observacoes']
        ],
        'paciente_nome' => $guia['paciente_nome'],
        'paciente_documento' => $guia['paciente_documento'],
        'data_agendamento' => $guia['data_agendamento'],
        'horario_agendamento' => $guia['horario_agendamento'],
        'observacoes' => $guia['observacoes'],
        'status' => $guia['status'],
        'data_emissao' => date('d/m/Y', strtotime($guia['data_emissao']))
    ];

    // Limpar todos os buffers de saída antes de renderizar
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Renderiza o template para impressão
    include GUIAS_TEMPLATE_PATH . '/imprimir.php';
    exit;
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao imprimir guia: ' . $e->getMessage()
    ];

    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}
