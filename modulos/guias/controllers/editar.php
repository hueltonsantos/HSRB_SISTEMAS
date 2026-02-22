<?php
/**
 * Controlador para editar status da guia
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
    // Usa o Database singleton (suporta Docker e localhost)
    $db = Database::getInstance()->getConnection();

    // Busca informações da guia
    $stmt = $db->prepare("
        SELECT g.*, p.nome as paciente_nome, vp.procedimento as procedimento_nome
        FROM guias_encaminhamento g
        INNER JOIN pacientes p ON g.paciente_id = p.id
        INNER JOIN valores_procedimentos vp ON g.procedimento_id = vp.id
        WHERE g.id = ?
        LIMIT 1
    ");
    $stmt->execute([$guiaId]);
    $guia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$guia) {
        throw new Exception("Guia não encontrada");
    }
    
    // Se for um POST, processa a atualização
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Valida os dados
        if (!isset($_POST['status']) || empty($_POST['status'])) {
            throw new Exception("Status não informado");
        }
        
        $novoStatus = $_POST['status'];
        $observacoes = isset($_POST['observacoes']) ? $_POST['observacoes'] : $guia['observacoes'];
        
        // Atualiza o status da guia
        $stmtUpdate = $db->prepare("
            UPDATE guias_encaminhamento
            SET status = ?, observacoes = ?
            WHERE id = ?
        ");
        $stmtUpdate->execute([$novoStatus, $observacoes, $guiaId]);
        
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Status da guia atualizado com sucesso!'
        ];
        
        // Redireciona para a visualização
        header("Location: index.php?module=guias&action=view&id={$guiaId}");
        exit;
    }
    
    // Define o título da página
    $pageTitle = "Editar Guia #" . $guia['codigo'];
    
    // Inclui o template
    include GUIAS_TEMPLATE_PATH . '/editar.php';
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao editar guia: ' . $e->getMessage()
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}