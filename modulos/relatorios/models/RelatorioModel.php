<?php
class RelatorioModel extends Model {
    
    public function getDadosFinanceiros($inicio, $fim, $clinica_id = null) {
        $sql = "SELECT 
                    a.data_consulta as data_emissao,
                    a.id as codigo, -- Usando ID do agendamento como código
                    p.nome as paciente,
                    vp.procedimento,
                    vp.valor_paciente,
                    vp.valor_repasse,
                    (vp.valor_paciente - vp.valor_repasse) as lucro,
                    c.nome as clinica,
                    e.nome as especialidade
                FROM agendamentos a
                JOIN pacientes p ON a.paciente_id = p.id
                JOIN clinicas_parceiras c ON a.clinica_id = c.id
                JOIN especialidades e ON a.especialidade_id = e.id
                LEFT JOIN valores_procedimentos vp ON a.procedimento_id = vp.id
                WHERE a.data_consulta BETWEEN ? AND ? 
                AND a.status_agendamento != 'cancelado'";
        
        $params = [$inicio, $fim];
        
        if ($clinica_id) {
            $sql .= " AND c.id = ?";
            $params[] = $clinica_id;
        }
        
        $sql .= " ORDER BY a.data_consulta DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getDadosOperacionais($inicio, $fim) {
        // Query detalhada para a tabela operacional
        $sql = "SELECT 
                    e.nome as especialidade,
                    vp.procedimento,
                    c.nome as clinica,
                    COUNT(a.id) as qtd,
                    SUM(vp.valor_paciente) as valor_total
                FROM agendamentos a
                JOIN especialidades e ON a.especialidade_id = e.id
                JOIN clinicas_parceiras c ON a.clinica_id = c.id
                LEFT JOIN valores_procedimentos vp ON a.procedimento_id = vp.id
                WHERE a.data_consulta BETWEEN ? AND ?
                AND a.status_agendamento != 'cancelado'
                GROUP BY e.nome, vp.procedimento, c.nome
                ORDER BY qtd DESC";
                
        return $this->db->fetchAll($sql, [$inicio, $fim]);
    }

    public function getEstatisticasOperacionais($inicio, $fim) {
        $stats = [];

        // Clínica com mais agendamentos
        $sqlClinica = "SELECT c.nome, COUNT(a.id) as total 
                       FROM agendamentos a 
                       JOIN clinicas_parceiras c ON a.clinica_id = c.id 
                       WHERE a.data_consulta BETWEEN ? AND ? 
                       AND a.status_agendamento != 'cancelado'
                       GROUP BY c.nome 
                       ORDER BY total DESC LIMIT 1";
        $resClinica = $this->db->fetchOne($sqlClinica, [$inicio, $fim]);
        $stats['top_clinica'] = $resClinica ? $resClinica : ['nome' => 'N/A', 'total' => 0];

        // Especialidade mais procurada
        $sqlEsp = "SELECT e.nome, COUNT(a.id) as total 
                   FROM agendamentos a 
                   JOIN especialidades e ON a.especialidade_id = e.id 
                   WHERE a.data_consulta BETWEEN ? AND ? 
                   AND a.status_agendamento != 'cancelado'
                   GROUP BY e.nome 
                   ORDER BY total DESC LIMIT 1";
        $resEsp = $this->db->fetchOne($sqlEsp, [$inicio, $fim]);
        $stats['top_especialidade'] = $resEsp ? $resEsp : ['nome' => 'N/A', 'total' => 0];

        // Total Geral de Agendamentos no período
        $sqlTotal = "SELECT COUNT(id) as total FROM agendamentos 
                     WHERE data_consulta BETWEEN ? AND ? 
                     AND status_agendamento != 'cancelado'";
        $resTotal = $this->db->fetchOne($sqlTotal, [$inicio, $fim]);
        $stats['total_geral'] = $resTotal ? $resTotal['total'] : 0;

        return $stats;
    }

    public function getDashboardStats() {
        $stats = [];
        $inicio = date('Y-m-d', strtotime('-30 days'));
        $fim = date('Y-m-d');

        // 1. Volume por Clínica (Top 5)
        $sqlClinicas = "SELECT c.nome, COUNT(a.id) as total 
                        FROM agendamentos a 
                        JOIN clinicas_parceiras c ON a.clinica_id = c.id 
                        WHERE a.data_consulta BETWEEN ? AND ? 
                        AND a.status_agendamento != 'cancelado'
                        GROUP BY c.nome 
                        ORDER BY total DESC LIMIT 5";
        $stats['clinicas'] = $this->db->fetchAll($sqlClinicas, [$inicio, $fim]);

        // 2. Distribuição de Status (Qualidade)
        $sqlStatus = "SELECT status_agendamento, COUNT(id) as total 
                      FROM agendamentos 
                      WHERE data_consulta BETWEEN ? AND ?
                      GROUP BY status_agendamento";
        $stats['status'] = $this->db->fetchAll($sqlStatus, [$inicio, $fim]);

        return $stats;
    }
}
