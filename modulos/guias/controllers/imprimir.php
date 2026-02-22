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
<<<<<<< HEAD
    foreach ($configModel->listar() as $c) {
=======
    foreach($configModel->listar() as $c) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        $configs[$c['chave']] = $c['valor'];
    }

    // Busca informações da guia
    $stmt = $db->prepare("
        SELECT g.*, p.nome as paciente_nome, p.cpf as paciente_documento,
               vp.procedimento as procedimento_nome, vp.valor_paciente as procedimento_valor,
               e.nome as especialidade_nome, cp.nome as clinica_nome,
<<<<<<< HEAD
               cp.endereco, cp.telefone,
               vp.id as procedimento_id_orig
        FROM guias_encaminhamento g
        INNER JOIN pacientes p ON g.paciente_id = p.id
        LEFT JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
        LEFT JOIN especialidades e ON vp.especialidade_id = e.id
=======
               cp.endereco, cp.telefone
        FROM guias_encaminhamento g
        INNER JOIN pacientes p ON g.paciente_id = p.id
        INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
        INNER JOIN especialidades e ON vp.especialidade_id = e.id
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        LEFT JOIN clinicas_parceiras cp ON e.id = cp.id
        WHERE g.id = ?
        LIMIT 1
    ");
    $stmt->execute([$guiaId]);
    $guia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guia) {
        throw new Exception("Guia não encontrada");
    }

    // Busca procedimentos adicionais se houver agendamento vinculado
    $procedimentos = [];

    // Se tem agendamento_id, busca todos os procedimentos daquele agendamento
    if (!empty($guia['agendamento_id'])) {
        $stmtProc = $db->prepare("
            SELECT vp.procedimento, e.nome as especialidade_nome, c.nome as clinica_nome, 
                   c.endereco, c.telefone, c.observacoes as clinica_observacoes
            FROM agendamento_procedimentos ap
            JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
            JOIN especialidades e ON vp.especialidade_id = e.id
            JOIN agendamentos a ON ap.agendamento_id = a.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            WHERE ap.agendamento_id = ?
        ");
        $stmtProc->execute([$guia['agendamento_id']]);
        $procedimentos = $stmtProc->fetchAll(PDO::FETCH_ASSOC);
    }

    // Se não encontrou procedimentos via agendamento (ou não tem agendamento_id), usa o da guia
    if (empty($procedimentos)) {
        $procedimentos[] = [
            'procedimento' => $guia['procedimento_nome'],
            'especialidade_nome' => $guia['especialidade_nome'],
            'clinica_nome' => $guia['clinica_nome'],
            'endereco' => $guia['endereco'],
            'telefone' => $guia['telefone'],
            'clinica_observacoes' => '' // Fallback
        ];
    }

    // Formata os dados para o template de impressão
    $guiaData = [
        'id' => $guia['id'],
        'codigo' => $guia['codigo'],
        'procedimentos' => $procedimentos, // Lista de procedimentos
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
