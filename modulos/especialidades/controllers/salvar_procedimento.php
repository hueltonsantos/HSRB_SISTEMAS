<?php
/**
 * Controlador para salvar procedimento
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Verifica se os dados necessários foram informados
if (!isset($_POST['especialidade_id']) || empty($_POST['especialidade_id']) ||
    !isset($_POST['procedimento']) || empty($_POST['procedimento']) ||
    !isset($_POST['valor'])) {
    
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Dados incompletos para o procedimento'
    ];
    
    // Salva os dados do formulário para recuperá-los
    $_SESSION['form_data'] = $_POST;
    
    // Redireciona de volta para o formulário
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        header('Location: index.php?module=especialidades&action=edit_procedimento&id=' . $_POST['id']);
    } else {
        header('Location: index.php?module=especialidades&action=add_procedimento&especialidade_id=' . $_POST['especialidade_id']);
    }
    exit;
}

// Obtém os dados do formulário
$data = [
    'especialidade_id' => (int) $_POST['especialidade_id'],
    'procedimento' => trim($_POST['procedimento']),
    'valor' => str_replace(',', '.', $_POST['valor']),
    'status' => isset($_POST['status']) ? (int) $_POST['status'] : 1
];

// Se for uma edição, inclui o ID
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $data['id'] = (int) $_POST['id'];
}

try {
    // Conecta ao banco de dados
    $db = new PDO('mysql:host=localhost;dbname=clinica_encaminhamento', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Nome correto da tabela
    $tableName = 'valores_procedimentos';
    
    // Verifica se já existe um procedimento com este nome para esta especialidade
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE especialidade_id = ? AND procedimento = ?" . 
                          (isset($data['id']) ? " AND id != ?" : ""));
    
    $params = [$data['especialidade_id'], $data['procedimento']];
    if (isset($data['id'])) {
        $params[] = $data['id'];
    }
    
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Se já existe, retorna erro
    if ((int) $result['total'] > 0) {
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Já existe um procedimento com este nome para esta especialidade'
        ];
        
        // Salva os dados do formulário para recuperá-los
        $_SESSION['form_data'] = $_POST;
        
        // Redireciona de volta para o formulário
        if (isset($data['id'])) {
            header('Location: index.php?module=especialidades&action=edit_procedimento&id=' . $data['id']);
        } else {
            header('Location: index.php?module=especialidades&action=add_procedimento&especialidade_id=' . $data['especialidade_id']);
        }
        exit;
    }
    
    // Se for uma edição, atualiza o registro
    if (isset($data['id'])) {
        $stmt = $db->prepare("UPDATE $tableName SET procedimento = ?, valor = ?, status = ? WHERE id = ?");
        $stmt->execute([$data['procedimento'], $data['valor'], $data['status'], $data['id']]);
        
        $message = 'Procedimento atualizado com sucesso!';
    } 
    // Senão, insere um novo
    else {
        $stmt = $db->prepare("INSERT INTO $tableName (especialidade_id, procedimento, valor, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['especialidade_id'], $data['procedimento'], $data['valor'], $data['status']]);
        
        $message = 'Procedimento adicionado com sucesso!';
    }
    
    $_SESSION['mensagem'] = [
        'tipo' => 'success',
        'texto' => $message
    ];
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao salvar procedimento: ' . $e->getMessage()
    ];
    
    // Salva os dados do formulário para recuperá-los
    $_SESSION['form_data'] = $_POST;
    
    // Redireciona de volta para o formulário
    if (isset($data['id'])) {
        header('Location: index.php?module=especialidades&action=edit_procedimento&id=' . $data['id']);
    } else {
        header('Location: index.php?module=especialidades&action=add_procedimento&especialidade_id=' . $data['especialidade_id']);
    }
    exit;
}

// Redireciona para a lista de procedimentos
header('Location: index.php?module=especialidades&action=procedimentos&id=' . $data['especialidade_id']);
exit;