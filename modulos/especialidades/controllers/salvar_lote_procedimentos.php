<?php
/**
 * Controlador para salvar procedimentos em lote
 */

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Verifica se os dados foram informados
if (!isset($_POST['especialidade_id']) || empty($_POST['especialidade_id']) || 
    !isset($_POST['procedimentos']) || empty($_POST['procedimentos'])) {
    
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Dados incompletos para adicionar procedimentos'
    ];
    
    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém os dados do formulário
$especialidadeId = (int) $_POST['especialidade_id'];
$procedimentosText = trim($_POST['procedimentos']);
$status = isset($_POST['status']) ? (int) $_POST['status'] : 1;

// Divide o texto em linhas
$linhas = explode("\n", $procedimentosText);

// Prepara a conexão com o banco
try {
    $db = new PDO('mysql:host=localhost;dbname=clinica_encaminhamento', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Nome correto da tabela
    $tableName = 'valores_procedimentos';
    
    // Prepara a inserção
    $stmt = $db->prepare("INSERT INTO $tableName (especialidade_id, procedimento, valor, status) VALUES (?, ?, ?, ?)");
    
    // Contadores
    $countTotal = 0;
    $countSuccess = 0;
    $countError = 0;
    
    // Processa cada linha
    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if (empty($linha)) continue;
        
        $countTotal++;
        
        // Divide a linha em nome e valor
        $partes = explode('|', $linha);
        
        if (count($partes) < 2) {
            $countError++;
            continue; // Ignora linhas com formato inválido
        }
        
        $nome = trim($partes[0]);
        $valor = trim($partes[1]);
        
        // Converte para o formato decimal do banco
        $valor = str_replace(',', '.', $valor);
        
        // Tenta inserir
        try {
            $result = $stmt->execute([$especialidadeId, $nome, $valor, $status]);
            if ($result) {
                $countSuccess++;
            } else {
                $countError++;
            }
        } catch (Exception $e) {
            $countError++;
        }
    }
    
    // Define a mensagem de sucesso
    if ($countSuccess > 0) {
        $_SESSION['mensagem'] = [
            'tipo' => 'success',
            'texto' => "Procedimentos adicionados com sucesso! ($countSuccess de $countTotal)"
        ];
        
        if ($countError > 0) {
            $_SESSION['mensagem']['texto'] .= " - $countError procedimento(s) não foram adicionados devido a erros.";
        }
    } else {
        $_SESSION['mensagem'] = [
            'tipo' => 'warning',
            'texto' => "Nenhum procedimento foi adicionado. Verifique o formato e tente novamente."
        ];
    }
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao adicionar procedimentos: ' . $e->getMessage()
    ];
}

// Redireciona para a lista de procedimentos
header('Location: index.php?module=especialidades&action=procedimentos&id=' . $especialidadeId);
exit;