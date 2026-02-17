<?php
/**
 * Model para Minha Clinica (Master)
 */
class MinhaClinicaModel extends Model {

    // ==================== ESPECIALIDADES ====================

    public function getEspecialidades($apenasAtivas = false) {
        $sql = "SELECT * FROM master_especialidades";
        if ($apenasAtivas) {
            $sql .= " WHERE status = 1";
        }
        $sql .= " ORDER BY nome ASC";
        return $this->db->fetchAll($sql);
    }

    public function getEspecialidade($id) {
        return $this->db->fetchOne("SELECT * FROM master_especialidades WHERE id = ?", [$id]);
    }

    public function salvarEspecialidade($data) {
        if (!empty($data['id'])) {
            $this->db->update('master_especialidades', [
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'status' => $data['status'] ?? 1
            ], 'id = ?', [$data['id']]);
            return $data['id'];
        } else {
            return $this->db->insert('master_especialidades', [
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'status' => $data['status'] ?? 1
            ]);
        }
    }

    public function deletarEspecialidade($id) {
        return $this->db->delete('master_especialidades', 'id = ?', [$id]);
    }

    // ==================== PROCEDIMENTOS ====================

    public function getProcedimentos($especialidadeId = null, $apenasAtivos = false) {
        $sql = "SELECT p.*, e.nome as especialidade_nome
                FROM master_procedimentos p
                LEFT JOIN master_especialidades e ON p.especialidade_id = e.id
                WHERE 1=1";
        $params = [];

        if ($especialidadeId) {
            $sql .= " AND p.especialidade_id = ?";
            $params[] = $especialidadeId;
        }
        if ($apenasAtivos) {
            $sql .= " AND p.status = 1";
        }
        $sql .= " ORDER BY e.nome, p.procedimento ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getProcedimento($id) {
        return $this->db->fetchOne("SELECT * FROM master_procedimentos WHERE id = ?", [$id]);
    }

    public function salvarProcedimento($data) {
        if (!empty($data['id'])) {
            $this->db->update('master_procedimentos', [
                'especialidade_id' => $data['especialidade_id'],
                'procedimento' => $data['procedimento'],
                'valor' => $data['valor'],
                'duracao_minutos' => $data['duracao_minutos'] ?? 30,
                'status' => $data['status'] ?? 1
            ], 'id = ?', [$data['id']]);
            return $data['id'];
        } else {
            return $this->db->insert('master_procedimentos', [
                'especialidade_id' => $data['especialidade_id'],
                'procedimento' => $data['procedimento'],
                'valor' => $data['valor'],
                'duracao_minutos' => $data['duracao_minutos'] ?? 30,
                'status' => $data['status'] ?? 1
            ]);
        }
    }

    public function deletarProcedimento($id) {
        return $this->db->delete('master_procedimentos', 'id = ?', [$id]);
    }

    // ==================== PROFISSIONAIS ====================

    public function getProfissionais($especialidadeId = null, $apenasAtivos = false) {
        $sql = "SELECT p.*, e.nome as especialidade_nome
                FROM master_profissionais p
                LEFT JOIN master_especialidades e ON p.especialidade_id = e.id
                WHERE 1=1";
        $params = [];

        if ($especialidadeId) {
            $sql .= " AND p.especialidade_id = ?";
            $params[] = $especialidadeId;
        }
        if ($apenasAtivos) {
            $sql .= " AND p.status = 1";
        }
        $sql .= " ORDER BY p.nome ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getProfissional($id) {
        return $this->db->fetchOne("SELECT * FROM master_profissionais WHERE id = ?", [$id]);
    }

    public function salvarProfissional($data) {
        if (!empty($data['id'])) {
            $this->db->update('master_profissionais', [
                'nome' => $data['nome'],
                'especialidade_id' => $data['especialidade_id'] ?? null,
                'registro_profissional' => $data['registro_profissional'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'email' => $data['email'] ?? null,
                'status' => $data['status'] ?? 1
            ], 'id = ?', [$data['id']]);
            return $data['id'];
        } else {
            return $this->db->insert('master_profissionais', [
                'nome' => $data['nome'],
                'especialidade_id' => $data['especialidade_id'] ?? null,
                'registro_profissional' => $data['registro_profissional'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'email' => $data['email'] ?? null,
                'status' => $data['status'] ?? 1
            ]);
        }
    }

    public function deletarProfissional($id) {
        return $this->db->delete('master_profissionais', 'id = ?', [$id]);
    }

    // ==================== AGENDAMENTOS ====================

    public function getAgendamentos($filtros = [], $limite = 100) {
        $sql = "SELECT a.*,
                       p.nome as paciente_nome,
                       p.celular as paciente_celular,
                       e.nome as especialidade_nome,
                       pr.procedimento as procedimento_nome,
                       prof.nome as profissional_nome
                FROM master_agendamentos a
                LEFT JOIN pacientes p ON a.paciente_id = p.id
                LEFT JOIN master_especialidades e ON a.especialidade_id = e.id
                LEFT JOIN master_procedimentos pr ON a.procedimento_id = pr.id
                LEFT JOIN master_profissionais prof ON a.profissional_id = prof.id
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND a.data_consulta >= ?";
            $params[] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $sql .= " AND a.data_consulta <= ?";
            $params[] = $filtros['data_fim'];
        }
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filtros['especialidade_id'];
        }
        if (!empty($filtros['profissional_id'])) {
            $sql .= " AND a.profissional_id = ?";
            $params[] = $filtros['profissional_id'];
        }
        if (!empty($filtros['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filtros['status'];
        }
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND a.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }

        $sql .= " ORDER BY a.data_consulta DESC, a.hora_consulta DESC LIMIT " . intval($limite);

        return $this->db->fetchAll($sql, $params);
    }

    public function getAgendamento($id) {
        $sql = "SELECT a.*,
                       p.nome as paciente_nome,
                       p.celular as paciente_celular,
                       p.telefone_fixo as paciente_telefone,
                       e.nome as especialidade_nome,
                       pr.procedimento as procedimento_nome,
                       pr.valor as procedimento_valor,
                       prof.nome as profissional_nome
                FROM master_agendamentos a
                LEFT JOIN pacientes p ON a.paciente_id = p.id
                LEFT JOIN master_especialidades e ON a.especialidade_id = e.id
                LEFT JOIN master_procedimentos pr ON a.procedimento_id = pr.id
                LEFT JOIN master_profissionais prof ON a.profissional_id = prof.id
                WHERE a.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function salvarAgendamento($data) {
        $agendamentoData = [
            'paciente_id' => $data['paciente_id'],
            'especialidade_id' => $data['especialidade_id'],
            'procedimento_id' => $data['procedimento_id'] ?? null,
            'profissional_id' => $data['profissional_id'] ?? null,
            'data_consulta' => $data['data_consulta'],
            'hora_consulta' => $data['hora_consulta'],
            'status' => $data['status'] ?? 'agendado',
            'valor' => $data['valor'] ?? 0,
            'forma_pagamento' => $data['forma_pagamento'] ?? null,
            'observacoes' => $data['observacoes'] ?? null
        ];

        if (!empty($data['id'])) {
            $this->db->update('master_agendamentos', $agendamentoData, 'id = ?', [$data['id']]);
            return $data['id'];
        } else {
            return $this->db->insert('master_agendamentos', $agendamentoData);
        }
    }

    public function atualizarStatusAgendamento($id, $status) {
        return $this->db->update('master_agendamentos', ['status' => $status], 'id = ?', [$id]);
    }

    public function deletarAgendamento($id) {
        return $this->db->delete('master_agendamentos', 'id = ?', [$id]);
    }

    // Salvar procedimentos do agendamento (multiplos)
    public function salvarAgendamentoProcedimentos($agendamentoId, $procedimentos) {
        // Remove procedimentos anteriores
        $this->db->delete('master_agendamento_procedimentos', 'agendamento_id = ?', [$agendamentoId]);

        // Insere novos procedimentos
        foreach ($procedimentos as $procedimentoId) {
            $proc = $this->getProcedimento($procedimentoId);
            $valor = $proc ? $proc['valor'] : 0;

            $this->db->insert('master_agendamento_procedimentos', [
                'agendamento_id' => $agendamentoId,
                'procedimento_id' => $procedimentoId,
                'valor' => $valor
            ]);
        }
    }

    // Buscar procedimentos de um agendamento
    public function getAgendamentoProcedimentos($agendamentoId) {
        $sql = "SELECT ap.*, p.procedimento, p.duracao_minutos
                FROM master_agendamento_procedimentos ap
                LEFT JOIN master_procedimentos p ON ap.procedimento_id = p.id
                WHERE ap.agendamento_id = ?";
        return $this->db->fetchAll($sql, [$agendamentoId]);
    }

    // ==================== DASHBOARD / ESTATISTICAS ====================

    public function getEstatisticasHoje() {
        $hoje = date('Y-m-d');

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'agendado' THEN 1 ELSE 0 END) as agendados,
                    SUM(CASE WHEN status = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
                    SUM(CASE WHEN status = 'realizado' THEN 1 ELSE 0 END) as realizados,
                    SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(CASE WHEN status = 'faltou' THEN 1 ELSE 0 END) as faltaram,
                    COALESCE(SUM(CASE WHEN status = 'realizado' THEN valor ELSE 0 END), 0) as faturamento
                FROM master_agendamentos
                WHERE data_consulta = ?";

        return $this->db->fetchOne($sql, [$hoje]);
    }

    public function getEstatisticasMes($mes = null, $ano = null) {
        $mes = $mes ?? date('m');
        $ano = $ano ?? date('Y');

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'realizado' THEN 1 ELSE 0 END) as realizados,
                    SUM(CASE WHEN status = 'cancelado' OR status = 'faltou' THEN 1 ELSE 0 END) as perdidos,
                    COALESCE(SUM(CASE WHEN status = 'realizado' THEN valor ELSE 0 END), 0) as faturamento
                FROM master_agendamentos
                WHERE MONTH(data_consulta) = ? AND YEAR(data_consulta) = ?";

        return $this->db->fetchOne($sql, [$mes, $ano]);
    }

    public function getAgendamentosProximos($limite = 10) {
        $sql = "SELECT a.*,
                       p.nome as paciente_nome,
                       e.nome as especialidade_nome,
                       prof.nome as profissional_nome
                FROM master_agendamentos a
                LEFT JOIN pacientes p ON a.paciente_id = p.id
                LEFT JOIN master_especialidades e ON a.especialidade_id = e.id
                LEFT JOIN master_profissionais prof ON a.profissional_id = prof.id
                WHERE a.data_consulta >= CURDATE()
                  AND a.status IN ('agendado', 'confirmado')
                ORDER BY a.data_consulta ASC, a.hora_consulta ASC
                LIMIT " . intval($limite);

        return $this->db->fetchAll($sql);
    }

    public function getHorariosOcupados($data, $profissionalId = null) {
        $sql = "SELECT hora_consulta FROM master_agendamentos
                WHERE data_consulta = ?
                  AND status NOT IN ('cancelado', 'faltou')";
        $params = [$data];

        if ($profissionalId) {
            $sql .= " AND profissional_id = ?";
            $params[] = $profissionalId;
        }

        $result = $this->db->fetchAll($sql, $params);
        return array_column($result, 'hora_consulta');
    }
}
