<?php
/**
 * Controlador para cancelar uma guia
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Método de requisição inválido'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}

// Verifica se o ID foi informado
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID da guia não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}

// Obtém o ID da guia
$guiaId = (int) $_POST['id'];

try {
    // Usa o Database singleton (suporta Docker e localhost)
    $db = Database::getInstance()->getConnection();

    // Verifica se a guia existe
    $stmt = $db->prepare("SELECT id, status FROM guias_encaminhamento WHERE id = ?");
    $stmt->execute([$guiaId]);
    $guia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$guia) {
        throw new Exception("Guia não encontrada");
    }
    
    // Verifica se a guia já está cancelada
    if ($guia['status'] === 'cancelado') {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Esta guia já está cancelada'
        ];
        
        // Redireciona para a visualização
        header("Location: index.php?module=guias&action=view&id={$guiaId}");
        exit;
    }
    
    // Atualiza o status da guia para cancelado
    $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : 'Cancelado pelo operador';
    
    $stmtUpdate = $db->prepare("
        UPDATE guias_encaminhamento
        SET status = 'cancelado',
            observacoes = CONCAT(observacoes, '\n\nMotivo do cancelamento: ', ?)
        WHERE id = ?
    ");
    $stmtUpdate->execute([$motivo, $guiaId]);
    
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => 'Guia cancelada com sucesso!'
    ];
    
    // Redireciona para a visualização
    header("Location: index.php?module=guias&action=view&id={$guiaId}");
    exit;
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao cancelar guia: ' . $e->getMessage()
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=guias&action=list');
    exit;
}