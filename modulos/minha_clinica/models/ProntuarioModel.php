<?php
/**
 * Model para Prontuário Eletrônico e Evoluções
 * Implementa versionamento e assinatura digital
 */
class ProntuarioModel extends Model {
    protected $table = 'master_evolucoes';

    public function getEvolucoesPorPaciente($pacienteId, $apenasAtivas = true) {
        $sql = "SELECT e.*, p.nome as profissional_nome, p.registro_profissional
                FROM master_evolucoes e
                JOIN master_profissionais p ON e.profissional_id = p.id
                WHERE e.paciente_id = ?";
        
        if ($apenasAtivas) {
            $sql .= " AND e.ativo = 1";
        }
        
        $sql .= " ORDER BY e.data_registro DESC";
        
        return $this->db->fetchAll($sql, [$pacienteId]);
    }

    public function salvarEvolucao($data) {
        // Gera hash da evolução para garantir integridade
        // Hash inclui: texto, data, profissional e paciente
        $conteudoAssinar = $data['texto'] . date('Y-m-d H:i:s') . $data['profissional_id'] . $data['paciente_id'];
        $hash = hash('sha256', $conteudoAssinar);

        $novaVersao = [
            'paciente_id' => $data['paciente_id'],
            'profissional_id' => $data['profissional_id'],
            'agendamento_id' => $data['agendamento_id'] ?? null,
            'texto' => $data['texto'],
            'cid10' => $data['cid10'] ?? null,
            'data_registro' => date('Y-m-d H:i:s'),
            'ativo' => 1,
            'assinatura_digital_hash' => $hash
        ];

        // Lógica de Versionamento
        if (!empty($data['id_anterior'])) {
            // Busca a evolução anterior
            $evolucaoAnterior = $this->getById($data['id_anterior']);
            
            if ($evolucaoAnterior) {
                // Desativa a anterior
                $this->db->update($this->table, ['ativo' => 0], 'id = ?', [$evolucaoAnterior['id']]);

                // Define numero da versao e link com original
                $novaVersao['versao'] = $evolucaoAnterior['versao'] + 1;
                $novaVersao['id_original'] = $evolucaoAnterior['id_original'] ?? $evolucaoAnterior['id'];
            }
        } else {
            // Primeira versão
            $novaVersao['versao'] = 1;
            $novaVersao['id_original'] = null; // É a original
        }

        return $this->db->insert($this->table, $novaVersao);
    }

    public function getHistoricoVersoes($idOriginal) {
        // Busca todas as versões de uma evolução baseada no ID Original ou ID Atual
        // Se o ID passado não for o original, buscamos o original primeiro
        $registro = $this->getById($idOriginal);
        
        if (!$registro) return [];

        $rootId = $registro['id_original'] ?? $registro['id'];

        // Busca todas que tem esse id_original ou são o próprio id_original
        $sql = "SELECT e.*, p.nome as profissional_nome
                FROM master_evolucoes e
                JOIN master_profissionais p ON e.profissional_id = p.id
                WHERE (e.id = ? OR e.id_original = ?)
                ORDER BY e.versao DESC"; // Mais recente primeiro
        
        return $this->db->fetchAll($sql, [$rootId, $rootId]);
    }
    
    public function getEvolucao($id) {
        $sql = "SELECT e.*, p.nome as profissional_nome, p.registro_profissional, 
                       pac.nome as paciente_nome
                FROM master_evolucoes e
                JOIN master_profissionais p ON e.profissional_id = p.id
                JOIN pacientes pac ON e.paciente_id = pac.id
                WHERE e.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
}
