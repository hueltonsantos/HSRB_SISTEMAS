<?php
/**
 * Salvar Item de Tabela de Preço (AJAX)
 */

// Limpar qualquer buffer de saida do roteador
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

if (!hasPermission('minha_clinica_editar')) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $model = new ConveniosModel();

        $dados = [
            'convenio_id' => (int)$_POST['convenio_id'],
            'procedimento_id' => (int)$_POST['procedimento_id'],
            'valor' => str_replace(',', '.', $_POST['valor']),
            'codigo_tuss' => $_POST['codigo_tuss'] ?? null,
            'repasse_percentual' => !empty($_POST['repasse_percentual']) ? str_replace(',', '.', $_POST['repasse_percentual']) : null
        ];

        $id = $model->salvarPreco($dados);

        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Preço atualizado!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
exit;
