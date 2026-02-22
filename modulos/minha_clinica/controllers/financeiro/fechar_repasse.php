<?php
/**
 * Fechar Repasse (Gerar Contas a Pagar ao Médico)
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hasPermission('minha_clinica_financeiro')) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$db = Database::getInstance();
$profissionalId = $_POST['profissional_id'] ?? null;
$dataInicio = $_POST['data_inicio'] ?? null;
$dataFim = $_POST['data_fim'] ?? null;

if (!$profissionalId || !$dataInicio || !$dataFim) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    $db->beginTransaction();

    // 1. Recalcular itens (mesma lógica do relatório) para segurança
    $sql = "SELECT cr.*, 
                   pc.repasse_padrao,
                   conv.id as convenio_id,
                   (SELECT GROUP_CONCAT(proc.id) 
                    FROM master_agendamentos_procedimentos map 
                    JOIN master_procedimentos proc ON map.procedimento_id = proc.id 
                    JOIN master_agendamentos a ON map.agendamento_id = a.id
                    JOIN master_guias g ON a.id = g.agendamento_id
                    WHERE g.id = cr.guia_id) as procedimentos_ids
            FROM master_financeiro_caixa_realizado cr
            JOIN master_guias g ON cr.guia_id = g.id
            JOIN master_agendamentos a ON g.agendamento_id = a.id
            LEFT JOIN master_convenios conv ON a.convenio_id = conv.id
            LEFT JOIN master_profissionais_config pc ON a.profissional_id = pc.profissional_id
            WHERE a.profissional_id = ? 
              AND cr.data_recebimento BETWEEN ? AND ?
              AND cr.id NOT IN (SELECT caixa_realizado_id FROM master_financeiro_repasses_itens)";
    
    $itens = $db->fetchAll($sql, [$profissionalId, $dataInicio, $dataFim]);

    if (empty($itens)) {
        throw new Exception("Nenhum item pendente encontrado para fechar neste período.");
    }

    $totalProducao = 0;
    $totalRepasse = 0;
    $itensRepasse = [];

    foreach ($itens as $mov) {
        $valorBase = $mov['valor_recebido'];
        $percentualAplicado = $mov['repasse_padrao'] ?? 50;
        
        // Regra específica
        if ($mov['convenio_id'] && $mov['procedimentos_ids']) {
            $procIds = explode(',', $mov['procedimentos_ids']);
            $procIdPrincipal = $procIds[0];
            
            $sqlRegra = "SELECT repasse_percentual FROM master_tabela_precos 
                         WHERE convenio_id = ? AND procedimento_id = ? 
                         AND repasse_percentual IS NOT NULL";
            $regraEspecifica = $db->fetchOne($sqlRegra, [$mov['convenio_id'], $procIdPrincipal]);
            if ($regraEspecifica) {
                $percentualAplicado = $regraEspecifica['repasse_percentual'];
            }
        }
        
        $valorRepasse = $valorBase * ($percentualAplicado / 100);
        
        $totalProducao += $valorBase;
        $totalRepasse += $valorRepasse;
        
        $itensRepasse[] = [
            'caixa_realizado_id' => $mov['id'],
            'valor_base_item' => $valorBase,
            'percentual_aplicado' => $percentualAplicado,
            'valor_comissao' => $valorRepasse
        ];
    }

    // 2. Criar registro Mestre de Repasse
    $dadosRepasse = [
        'profissional_id' => $profissionalId,
        'periodo_inicio' => $dataInicio,
        'periodo_fim' => $dataFim,
        'valor_total_producao' => $totalProducao,
        'valor_liquido_repasse' => $totalRepasse,
        'status' => 'calculado', // Cria como pendente de pagamento
        'data_pagamento' => null
    ];
    
    $repasseId = $db->insert('master_financeiro_repasses', $dadosRepasse);

    // 3. Inserir Itens
    foreach ($itensRepasse as $item) {
        $item['repasse_id'] = $repasseId;
        $db->insert('master_financeiro_repasses_itens', $item);
    }

    $db->commit();
    echo json_encode(['success' => true, 'repasse_id' => $repasseId]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
