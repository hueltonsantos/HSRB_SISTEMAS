<?php

/**
 * Controlador para visualizar uma guia específica
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

// Obtém o ID da guia
$guiaId = (int) $_GET['id'];

try {
    // Conecta ao banco de dados
    $db = new PDO('mysql:host=localhost;dbname=clinica_encaminhamento', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



    // Modifique a consulta SQL para:
    $stmt = $db->prepare("
    SELECT g.*, p.nome as paciente_nome, p.cpf as paciente_documento,
           vp.procedimento as procedimento_nome, vp.valor as procedimento_valor,
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

    // Busca informações da guia
    // $stmt = $db->prepare("
    //     SELECT g.*, p.nome as paciente_nome, p.documento as paciente_documento,
    //            vp.procedimento as procedimento_nome, vp.valor as procedimento_valor,
    //            e.nome as especialidade_nome, cp.nome as clinica_nome,
    //            cp.endereco, cp.telefone, cp.observacoes as clinica_observacoes
    //     FROM guias_encaminhamento g
    //     INNER JOIN pacientes p ON g.paciente_id = p.id
    //     INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
    //     INNER JOIN especialidades e ON vp.especialidade_id = e.id
    //     LEFT JOIN clinicas_parceiras cp ON e.id = cp.especialidade_id
    //     WHERE g.id = ?
    //     LIMIT 1
    // ");
    $stmt->execute([$guiaId]);
    $guia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guia) {
        throw new Exception("Guia não encontrada");
    }

    // Define o título da página
    $pageTitle = "Guia de Encaminhamento #" . $guia['codigo'];

    // Inclui o template
    include GUIAS_TEMPLATE_PATH . '/visualizar.php';
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao visualizar guia: ' . $e->getMessage()
    ];

    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}
