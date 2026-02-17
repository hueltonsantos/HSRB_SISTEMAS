<?php
/**
 * Model para o Dashboard em Tempo Real
 * Contém todas as queries para gráficos e relatórios
 */
class DashboardRealtimeModel extends Model {
    protected $table = 'agendamentos';

    /**
     * Obtém totais financeiros do período
     */
    public function getTotaisFinanceiros($filtros = []) {
        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        // Query simplificada - usa valor_total da tabela agendamentos se não houver procedimentos
        $sql = "SELECT
                    COUNT(a.id) as total_agendamentos,
                    COALESCE(SUM(COALESCE(ap_totais.valor_proc, a.valor_total, 0)), 0) as faturamento_bruto,
                    COALESCE(SUM(COALESCE(ap_totais.repasse_proc, a.valor_total * 0.5, 0)), 0) as total_repasse,
                    COALESCE(SUM(COALESCE(ap_totais.valor_proc, a.valor_total, 0)) - SUM(COALESCE(ap_totais.repasse_proc, a.valor_total * 0.5, 0)), 0) as lucro_liquido
                FROM agendamentos a
                LEFT JOIN (
                    SELECT ap.agendamento_id,
                           SUM(ap.valor) as valor_proc,
                           SUM(COALESCE(vp.valor_repasse, ap.valor * 0.5)) as repasse_proc
                    FROM agendamento_procedimentos ap
                    LEFT JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
                    GROUP BY ap.agendamento_id
                ) ap_totais ON a.id = ap_totais.agendamento_id
                WHERE DATE(a.data_agendamento) BETWEEN ? AND ?
                  AND a.status_agendamento != 'cancelado'";

        $params = [$dataInicio, $dataFim];

        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filtros['especialidade_id'];
        }

        $result = $this->db->fetchOne($sql, $params);

        return $result ?: [
            'total_agendamentos' => 0,
            'faturamento_bruto' => 0,
            'total_repasse' => 0,
            'lucro_liquido' => 0
        ];
    }

    /**
     * Obtém agendamentos por usuário criador (via logs_sistema)
     */
    public function getAgendamentosPorUsuario($filtros = []) {
        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        $sql = "SELECT
                    COALESCE(l.usuario_nome, 'Sistema') as usuario,
                    l.usuario_id,
                    COUNT(DISTINCT l.registro_id) as total
                FROM logs_sistema l
                INNER JOIN agendamentos a ON l.registro_id = a.id
                WHERE l.modulo = 'agendamentos'
                  AND l.acao = 'criar'
                  AND DATE(l.data_hora) BETWEEN ? AND ?
                  AND a.status_agendamento != 'cancelado'";

        $params = [$dataInicio, $dataFim];

        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filtros['especialidade_id'];
        }

        // Filtro por usuário específico
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND l.usuario_id = ?";
            $params[] = $filtros['usuario_id'];
        }

        $sql .= " GROUP BY l.usuario_id, l.usuario_nome
                  ORDER BY total DESC
                  LIMIT 10";

        return $this->db->fetchAll($sql, $params) ?: [];
    }

    /**
     * Obtém agendamentos por especialidade
     */
    public function getAgendamentosPorEspecialidade($filtros = []) {
        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        $sql = "SELECT
                    e.id as especialidade_id,
                    e.nome as especialidade,
                    COUNT(a.id) as total,
                    COALESCE(SUM(a.valor_total), 0) as valor_total
                FROM agendamentos a
                LEFT JOIN especialidades e ON a.especialidade_id = e.id
                WHERE DATE(a.data_agendamento) BETWEEN ? AND ?
                  AND a.status_agendamento != 'cancelado'";

        $params = [$dataInicio, $dataFim];

        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        $sql .= " GROUP BY e.id, e.nome
                  ORDER BY total DESC";

        return $this->db->fetchAll($sql, $params) ?: [];
    }

    /**
     * Obtém top procedimentos
     */
    public function getAgendamentosPorProcedimento($filtros = []) {
        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        $sql = "SELECT
                    vp.id as procedimento_id,
                    vp.procedimento,
                    e.nome as especialidade,
                    COUNT(ap.id) as quantidade,
                    SUM(ap.valor) as valor_total,
                    SUM(vp.valor_repasse) as repasse_total,
                    SUM(ap.valor - vp.valor_repasse) as lucro
                FROM agendamento_procedimentos ap
                INNER JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
                INNER JOIN especialidades e ON vp.especialidade_id = e.id
                INNER JOIN agendamentos a ON ap.agendamento_id = a.id
                WHERE DATE(a.data_agendamento) BETWEEN ? AND ?
                  AND a.status_agendamento != 'cancelado'";

        $params = [$dataInicio, $dataFim];

        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND vp.especialidade_id = ?";
            $params[] = $filtros['especialidade_id'];
        }

        $sql .= " GROUP BY vp.id, vp.procedimento, e.nome
                  ORDER BY quantidade DESC
                  LIMIT 15";

        return $this->db->fetchAll($sql, $params) ?: [];
    }

    /**
     * Obtém distribuição por forma de pagamento
     */
    public function getDistribuicaoFormaPagamento($filtros = []) {
        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        $sql = "SELECT
                    CASE
                        WHEN a.forma_pagamento IS NULL OR a.forma_pagamento = '' THEN 'Não informado'
                        ELSE a.forma_pagamento
                    END AS forma_pagamento,
                    COUNT(a.id) as total,
                    COALESCE(SUM(a.valor_total), 0) as valor_total
                FROM agendamentos a
                WHERE DATE(a.data_agendamento) BETWEEN ? AND ?
                  AND a.status_agendamento != 'cancelado'";

        $params = [$dataInicio, $dataFim];

        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filtros['especialidade_id'];
        }

        $sql .= " GROUP BY forma_pagamento
                  ORDER BY total DESC";

        return $this->db->fetchAll($sql, $params) ?: [];
    }

    /**
     * Obtém lista detalhada de agendamentos
     */
    public function getListaDetalhada($filtros = [], $limit = 100) {
        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-d');
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        // Query simplificada sem depender de logs
        $sql = "SELECT
                    a.id,
                    a.data_consulta,
                    a.hora_consulta,
                    p.nome as paciente_nome,
                    c.nome as clinica_nome,
                    e.nome as especialidade_nome,
                    COALESCE(proc_list.procedimentos, '-') as procedimentos,
                    COALESCE(proc_list.valor_proc, a.valor_total, 0) as valor_total,
                    COALESCE(a.forma_pagamento, 'Não informado') as forma_pagamento,
                    a.status_agendamento,
                    'Sistema' as usuario_criador
                FROM agendamentos a
                LEFT JOIN pacientes p ON a.paciente_id = p.id
                LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
                LEFT JOIN especialidades e ON a.especialidade_id = e.id
                LEFT JOIN (
                    SELECT ap.agendamento_id,
                           GROUP_CONCAT(DISTINCT vp.procedimento SEPARATOR ', ') as procedimentos,
                           SUM(ap.valor) as valor_proc
                    FROM agendamento_procedimentos ap
                    LEFT JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
                    GROUP BY ap.agendamento_id
                ) proc_list ON a.id = proc_list.agendamento_id
                WHERE DATE(a.data_agendamento) BETWEEN ? AND ?
                  AND a.status_agendamento != 'cancelado'";

        $params = [$dataInicio, $dataFim];

        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filtros['especialidade_id'];
        }

        $sql .= " ORDER BY a.data_consulta DESC, a.hora_consulta DESC
                  LIMIT " . intval($limit);

        return $this->db->fetchAll($sql, $params) ?: [];
    }

    /**
     * Obtém lista de clínicas para filtro
     */
    public function getClinicas() {
        $sql = "SELECT id, nome FROM clinicas_parceiras WHERE status = 1 ORDER BY nome";
        return $this->db->fetchAll($sql) ?: [];
    }

    /**
     * Obtém lista de especialidades para filtro
     */
    public function getEspecialidades() {
        $sql = "SELECT id, nome FROM especialidades WHERE status = 1 ORDER BY nome";
        return $this->db->fetchAll($sql) ?: [];
    }

    /**
     * Obtém lista de usuários para filtro
     */
    public function getUsuarios() {
        $sql = "SELECT id, nome FROM usuarios WHERE status = 1 ORDER BY nome";
        return $this->db->fetchAll($sql) ?: [];
    }

    /**
     * Obtém todos os dados de uma vez (para carregamento inicial)
     */
    public function getAllData($filtros = []) {
        return [
            'totais' => $this->getTotaisFinanceiros($filtros),
            'por_usuario' => $this->getAgendamentosPorUsuario($filtros),
            'por_especialidade' => $this->getAgendamentosPorEspecialidade($filtros),
            'por_procedimento' => $this->getAgendamentosPorProcedimento($filtros),
            'por_pagamento' => $this->getDistribuicaoFormaPagamento($filtros),
            'lista' => $this->getListaDetalhada($filtros)
        ];
    }
}
