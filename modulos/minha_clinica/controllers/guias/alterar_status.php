<?php
/**
 * Alterar Status da Guia (AJAX)
 */
// Limpar buffers do roteador para retornar JSON puro
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

if (!hasPermission('minha_clinica_editar')) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;
$motivo = $_POST['motivo_glosa'] ?? null;

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$db = Database::getInstance();

try {
    $updateData = ['status' => $status];
    if ($status === 'glosada') {
        $updateData['motivo_glosa'] = $motivo;
    }
    
    // Se status for 'paga', gerar entrada no caixa realizado
    if ($status === 'paga') {
        require_once MINHA_CLINICA_PATH . '/models/FinanceiroModel.php';
        $financeiroModel = new FinanceiroModel();

        // Buscar dados da guia/agendamento para saber valor
        $guia = $db->fetchOne("SELECT g.*, a.valor, a.id as agendamento_id 
                               FROM master_guias g 
                               JOIN master_agendamentos a ON g.agendamento_id = a.id 
                               WHERE g.id = ?", [$id]);
        
        if ($guia) {
            // Buscar previsão financeira vinculada
            $previsao = $db->fetchOne("SELECT id FROM master_financeiro_caixa_previsto WHERE agendamento_id = ?", [$guia['agendamento_id']]);
            
            if ($previsao) {
                $dadosBaixa = [
                    'descricao' => "Recebimento Guia #" . $guia['numero_guia'],
                    'valor_recebido' => $guia['valor'], // Assume valor integral por enquanto
                    'data_recebimento' => date('Y-m-d'),
                    'forma_pagamento' => 'Transferencia', // Default
                    'guia_id' => $id,
                    'baixa_total' => true
                ];
                $financeiroModel->baixarRecebimento($previsao['id'], $dadosBaixa);
            }
        }
    }
    
    $db->update('master_guias', $updateData, 'id = ?', [$id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
