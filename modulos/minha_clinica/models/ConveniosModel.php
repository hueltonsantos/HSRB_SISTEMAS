<?php
/**
 * Model para Gestão de Convênios e Tabelas de Preço
 */
class ConveniosModel extends Model {
    protected $table = 'master_convenios';

    // ==================== CONVÊNIOS ====================

    /**
     * Override: master_convenios usa campo 'ativo' ao inves de 'status'
     */
    public function deactivate($id) {
        return $this->db->update($this->table, ['ativo' => 0], 'id = ?', [$id]);
    }

    public function salvar($data) {
        if (!empty($data['id'])) {
            $this->db->update($this->table, [
                'nome_fantasia' => $data['nome_fantasia'],
                'razao_social' => $data['razao_social'] ?? null,
                'cnpj' => $data['cnpj'] ?? null,
                'registro_ans' => $data['registro_ans'] ?? null,
                'dias_retorno' => $data['dias_retorno'] ?? 30,
                'prazo_recebimento_dias' => $data['prazo_recebimento_dias'] ?? 30,
                'ativo' => $data['ativo'] ?? 1
            ], 'id = ?', [$data['id']]);
            return $data['id'];
        } else {
            return $this->db->insert($this->table, [
                'nome_fantasia' => $data['nome_fantasia'],
                'razao_social' => $data['razao_social'] ?? null,
                'cnpj' => $data['cnpj'] ?? null,
                'registro_ans' => $data['registro_ans'] ?? null,
                'dias_retorno' => $data['dias_retorno'] ?? 30,
                'prazo_recebimento_dias' => $data['prazo_recebimento_dias'] ?? 30,
                'ativo' => $data['ativo'] ?? 1
            ]);
        }
    }

    // ==================== TABELA DE PREÇOS ====================

    public function getPrecosPorConvenio($convenioId) {
        $sql = "SELECT tp.*, p.procedimento as procedimento_nome, p.valor as valor_base, p.codigo_padrao
                FROM master_tabela_precos tp
                JOIN master_procedimentos p ON tp.procedimento_id = p.id
                WHERE tp.convenio_id = ?";
        return $this->db->fetchAll($sql, [$convenioId]);
    }

    public function salvarPreco($data) {
        // Verifica se já existe para update ou insert
        $existe = $this->db->fetchOne(
            "SELECT id FROM master_tabela_precos WHERE convenio_id = ? AND procedimento_id = ?", 
            [$data['convenio_id'], $data['procedimento_id']]
        );

        $dadosPreco = [
            'convenio_id' => $data['convenio_id'],
            'procedimento_id' => $data['procedimento_id'],
            'valor' => $data['valor'],
            'codigo_tuss' => $data['codigo_tuss'] ?? null,
            'codigo_interno' => $data['codigo_interno'] ?? null,
            'repasse_percentual' => !empty($data['repasse_percentual']) ? $data['repasse_percentual'] : null,
            'ativo' => 1
        ];

        if ($existe) {
            $this->db->update('master_tabela_precos', $dadosPreco, 'id = ?', [$existe['id']]);
            return $existe['id'];
        } else {
            return $this->db->insert('master_tabela_precos', $dadosPreco);
        }
    }

    public function getValorExato($convenioId, $procedimentoId) {
        // Busca na tabela específica do convênio
        $precoEspecifico = $this->db->fetchOne(
            "SELECT valor, repasse_percentual, codigo_tuss FROM master_tabela_precos 
             WHERE convenio_id = ? AND procedimento_id = ? AND ativo = 1",
            [$convenioId, $procedimentoId]
        );

        if ($precoEspecifico) {
            return $precoEspecifico;
        }

        // Se não achar, busca o valor base do procedimento (Particular)
        // Apenas se o convênio for NULL ou se a lógica de negócio permitir fallback
        // Neste caso, vamos retornar o valor base mas indicar que não é tabela de convênio
        $procedimento = $this->db->fetchOne(
            "SELECT valor, codigo_padrao FROM master_procedimentos WHERE id = ?", 
            [$procedimentoId]
        );

        return [
            'valor' => $procedimento['valor'],
            'repasse_percentual' => null, // Usa o padrão do profissional
            'codigo_tuss' => $procedimento['codigo_padrao']
        ];
    }
}
