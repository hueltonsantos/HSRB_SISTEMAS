<?php
/**
 * Model para Gestão Financeira (Caixa e Repasses)
 */
class FinanceiroModel extends Model {
    
    // ==================== CAIXA (PREVISTO E REALIZADO) ====================

    public function gerarPrevisaoRecebimento($dados) {
        $insertData = [
            'descricao' => $dados['descricao'],
            'valor_previsto' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'],
            'agendamento_id' => $dados['agendamento_id'] ?? null,
            'guia_id' => $dados['guia_id'] ?? null,
            'convenio_id' => $dados['convenio_id'] ?? null,
            'status' => 'pendente'
        ];
        return $this->db->insert('master_financeiro_caixa_previsto', $insertData);
    }

    public function baixarRecebimento($previsaoId, $dadosBaixa) {
        // Inicia transação
        $this->db->beginTransaction();

        try {
            // 1. Registra no Caixa Realizado
            $realizadoData = [
                'previsao_id' => $previsaoId,
                'descricao' => $dadosBaixa['descricao'], // Pode ser diferente da previsão (ex: "Recebimento Parcial")
                'valor_recebido' => $dadosBaixa['valor_recebido'],
                'data_recebimento' => $dadosBaixa['data_recebimento'],
                'forma_pagamento' => $dadosBaixa['forma_pagamento'],
                'observacoes' => $dadosBaixa['observacoes'] ?? null
            ];
            $realizadoId = $this->db->insert('master_financeiro_caixa_realizado', $realizadoData);

            // 2. Atualiza status do Previsto
            // Se valor recebido >= previsto, status = liquidado. Se não, continua pendente parcial (futuro) ou liquidado com desconto?
            // Regra simplificada: Liquidado se o usuário marcar "Baixa Total"
            if (!empty($dadosBaixa['baixa_total'])) {
                $this->db->update('master_financeiro_caixa_previsto', ['status' => 'liquidado'], 'id = ?', [$previsaoId]);
            }
            
            // 3. Atualiza Guia se houver
            if (!empty($dadosBaixa['guia_id'])) {
                $statusGuia = (!empty($dadosBaixa['glosa'])) ? 'glosada' : 'paga';
                $this->db->update('master_guias', ['status' => $statusGuia], 'id = ?', [$dadosBaixa['guia_id']]);
            }

            $this->db->commit();
            return $realizadoId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ==================== REPASSES ====================

    public function calcularRepassesPeriodo($inicio, $fim, $profissionalId = null) {
        /*
          Lógica de Cálculo de Repasse:
          1. Busca todos os recebimentos (Caixa REALIZADO) no período.
          2. Verifica a regra de repasse do profissional ou do procedimento/convênio.
          3. Gera um preview do cálculo.
        */
        
        $sql = "SELECT cr.*, 
                       cp.agendamento_id, cp.convenio_id, cp.guia_id,
                       a.profissional_id, 
                       p.nome as profissional_nome,
                       tp.repasse_percentual as repasse_override,
                       pc.repasse_padrao_percentual
                FROM master_financeiro_caixa_realizado cr
                JOIN master_financeiro_caixa_previsto cp ON cr.previsao_id = cp.id
                JOIN master_agendamentos a ON cp.agendamento_id = a.id
                JOIN master_profissionais p ON a.profissional_id = p.id
                LEFT JOIN master_profissionais_config pc ON p.id = pc.profissional_id
                LEFT JOIN master_tabela_precos tp ON (a.convenio_id = tp.convenio_id AND a.procedimento_id = tp.procedimento_id)
                WHERE cr.data_recebimento BETWEEN ? AND ?";
        
        $params = [$inicio, $fim];

        if ($profissionalId) {
            $sql .= " AND a.profissional_id = ?";
            $params[] = $profissionalId;
        }

        $recebimentos = $this->db->fetchAll($sql, $params);
        $repassesCalculados = [];

        foreach ($recebimentos as $rec) {
            // Define percentual: O especifício do convênio ganha do padrão do profissional
            $percentual = $rec['repasse_override'] ?? $rec['repasse_padrao_percentual'] ?? 0;
            
            $valorComissao = ($rec['valor_recebido'] * $percentual) / 100;
            
            $repassesCalculados[] = [
                'profissional_id' => $rec['profissional_id'],
                'profissional_nome' => $rec['profissional_nome'],
                'recebimento_id' => $rec['id'],
                'data_recebimento' => $rec['data_recebimento'],
                'valor_base' => $rec['valor_recebido'],
                'percentual' => $percentual,
                'valor_repasse' => $valorComissao,
                'origem' => $rec['descricao']
            ];
        }

        return $repassesCalculados;
    }

    public function salvarFechamentoRepasse($dadosRepasse, $itensRepasse) {
        $this->db->beginTransaction();
        try {
            // 1. Salva Cabeçalho
            $repasseId = $this->db->insert('master_financeiro_repasses', $dadosRepasse);

            // 2. Salva Itens
            foreach ($itensRepasse as $item) {
                $this->db->insert('master_financeiro_repasses_itens', [
                    'repasse_id' => $repasseId,
                    'caixa_realizado_id' => $item['recebimento_id'],
                    'valor_base_item' => $item['valor_base'],
                    'percentual_aplicado' => $item['percentual'],
                    'valor_comissao' => $item['valor_repasse']
                ]);
            }
            
            $this->db->commit();
            return $repasseId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
