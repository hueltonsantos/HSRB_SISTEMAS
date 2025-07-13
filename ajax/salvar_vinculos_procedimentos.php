<?php
require_once '../config.php';
require_once '../functions.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

// Inicializa o controlador
$controller = new ProcedimentoClinicaController($db);

// Processa os vínculos
$procedimento_id = $_POST['procedimento_id'];
$clinicas = $_POST['clinicas'] ?? [];
$erros = [];
$sucessos = 0;

foreach ($clinicas as $clinica_id => $dados) {
    // Se a clínica estiver marcada como ativa, salva ou atualiza o vínculo
    if (isset($dados['ativo']) && $dados['ativo'] == '1') {
        $dados_vinculo = [
            'procedimento_id' => $procedimento_id,
            'clinica_id' => $clinica_id,
            'valor' => $dados['valor'] ?? 0,
            'observacoes' => $dados['observacoes'] ?? '',
            'status' => 1
        ];
        
        $resultado = $controller->salvar($dados_vinculo);
        
        if (isset($resultado['erro'])) {
            $erros[] = $resultado['erro'];
        } else {
            $sucessos++;
        }
    } else {
        // Busca o vínculo existente e o exclui se encontrado
        $query = "SELECT id FROM procedimentos_clinicas WHERE procedimento_id = ? AND clinica_id = ?";
        $vinculo = $db->query($query, [$procedimento_id, $clinica_id])->fetch_assoc();
        
        if ($vinculo) {
            $controller->excluir($vinculo['id']);
        }
    }
}

// Retorna o resultado
if (empty($erros)) {
    echo json_encode(['sucesso' => "Vínculos salvos com sucesso! ($sucessos vínculos atualizados)"]);
} else {
    echo json_encode(['erro' => implode("<br>", $erros)]);
}