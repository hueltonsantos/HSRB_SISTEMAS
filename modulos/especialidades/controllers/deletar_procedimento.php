<?php
/**
 * Controlador para exclusão de procedimento
 */

// Verifica se o ID foi informado (em GET ou POST)
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

if (empty($id)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do procedimento não informado'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID do procedimento
$id = (int) $id;

// Obtém o ID da especialidade (para redirecionamento)
$especialidadeId = 0;
if (isset($_GET['especialidade_id'])) {
    $especialidadeId = (int) $_GET['especialidade_id'];
} elseif (isset($_POST['especialidade_id'])) {
    $especialidadeId = (int) $_POST['especialidade_id'];
}

try {
    // Usa o Database singleton (suporta Docker e localhost)
    $db = Database::getInstance()->getConnection();

    // Nome correto da tabela obtido da estrutura do banco de dados
    $tableName = 'valores_procedimentos';
    
    // Se não temos o ID da especialidade, busca do banco
    if ($especialidadeId == 0) {
        $stmt = $db->prepare("SELECT especialidade_id FROM $tableName WHERE id = ?");
        $stmt->execute([$id]);
        $procedimento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($procedimento) {
            $especialidadeId = $procedimento['especialidade_id'];
        }
    }
    
    // Exclui o procedimento
    $stmt = $db->prepare("DELETE FROM $tableName WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => 'Procedimento excluído com sucesso!'
        ];
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => 'Nenhum registro foi afetado.'
        ];
    }
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao excluir procedimento: ' . $e->getMessage()
    ];
}

// Redireciona para a lista de procedimentos
if ($especialidadeId > 0) {
    header('Location: index.php?module=especialidades&action=procedimentos&id=' . $especialidadeId);
} else {
    header('Location: index.php?module=especialidades&action=list');
}
exit;