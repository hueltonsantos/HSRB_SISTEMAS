<?php
/**
 * Model para Minha Clinica (Master)
 */
<<<<<<< HEAD
class MinhaClinicaModel extends Model
{

    // ==================== ESPECIALIDADES ====================

    public function getEspecialidades($apenasAtivas = false)
    {
=======
class MinhaClinicaModel extends Model {

    // ==================== ESPECIALIDADES ====================

    public function getEspecialidades($apenasAtivas = false) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        $sql = "SELECT * FROM master_especialidades";
        if ($apenasAtivas) {
            $sql .= " WHERE status = 1";
        }
        $sql .= " ORDER BY nome ASC";
        return $this->db->fetchAll($sql);
    }

<<<<<<< HEAD
    public function getEspecialidade($id)
    {
        return $this->db->fetchOne("SELECT * FROM master_especialidades WHERE id = ?", [$id]);
    }

    public function salvarEspecialidade($data)
    {
=======
    public function getEspecialidade($id) {
        return $this->db->fetchOne("SELECT * FROM master_especialidades WHERE id = ?", [$id]);
    }

    public function salvarEspecialidade($data) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function deletarEspecialidade($id)
    {
=======
    public function deletarEspecialidade($id) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        return $this->db->delete('master_especialidades', 'id = ?', [$id]);
    }

    // ==================== PROCEDIMENTOS ====================

<<<<<<< HEAD
    public function getProcedimentos($especialidadeId = null, $apenasAtivos = false)
    {
=======
    public function getProcedimentos($especialidadeId = null, $apenasAtivos = false) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function getProcedimento($id)
    {
        return $this->db->fetchOne("SELECT * FROM master_procedimentos WHERE id = ?", [$id]);
    }

    public function salvarProcedimento($data)
    {
=======
    public function getProcedimento($id) {
        return $this->db->fetchOne("SELECT * FROM master_procedimentos WHERE id = ?", [$id]);
    }

    public function salvarProcedimento($data) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function deletarProcedimento($id)
    {
=======
    public function deletarProcedimento($id) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        return $this->db->delete('master_procedimentos', 'id = ?', [$id]);
    }

    // ==================== PROFISSIONAIS ====================

<<<<<<< HEAD
    public function getProfissionais($especialidadeId = null, $apenasAtivos = false)
    {
=======
    public function getProfissionais($especialidadeId = null, $apenasAtivos = false) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function getProfissional($id)
    {
        return $this->db->fetchOne("SELECT * FROM master_profissionais WHERE id = ?", [$id]);
    }

    public function salvarProfissional($data)
    {
=======
    public function getProfissional($id) {
        return $this->db->fetchOne("SELECT * FROM master_profissionais WHERE id = ?", [$id]);
    }

    public function salvarProfissional($data) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function deletarProfissional($id)
    {
=======
    public function deletarProfissional($id) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        return $this->db->delete('master_profissionais', 'id = ?', [$id]);
    }

    // ==================== AGENDAMENTOS ====================

<<<<<<< HEAD
    public function getAgendamentos($filtros = [], $limite = 100)
    {
=======
    public function getAgendamentos($filtros = [], $limite = 100) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function getAgendamento($id)
    {
=======
    public function getAgendamento($id) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        $sql = "SELECT a.*,
                       p.nome as paciente_nome,
                       p.celular as paciente_celular,
                       p.telefone_fixo as paciente_telefone,
                       e.nome as especialidade_nome,
                       pr.procedimento as procedimento_nome,
                       pr.valor as procedimento_valor,
<<<<<<< HEAD
                       prof.nome as profissional_nome,
                       c.nome_fantasia as convenio_nome
=======
                       prof.nome as profissional_nome
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                FROM master_agendamentos a
                LEFT JOIN pacientes p ON a.paciente_id = p.id
                LEFT JOIN master_especialidades e ON a.especialidade_id = e.id
                LEFT JOIN master_procedimentos pr ON a.procedimento_id = pr.id
                LEFT JOIN master_profissionais prof ON a.profissional_id = prof.id
<<<<<<< HEAD
                LEFT JOIN master_convenios c ON a.convenio_id = c.id
                WHERE a.id = ?";

        $agendamento = $this->db->fetchOne($sql, [$id]);

        if ($agendamento) {
            // Buscar procedimentos multiplos
            $sqlProc = "SELECT mp.procedimento, mp.codigo_padrao as codigo, map.valor 
                        FROM master_agendamento_procedimentos map
                        JOIN master_procedimentos mp ON map.procedimento_id = mp.id
                        WHERE map.agendamento_id = ?";
            $procedimentos = $this->db->fetchAll($sqlProc, [$id]);
            $agendamento['procedimentos_lista'] = $procedimentos;
        }

        return $agendamento;
    }

    public function salvarAgendamento($data)
    {
        $agendamentoData = [
            'paciente_id' => $data['paciente_id'],
            'convenio_id' => $data['convenio_id'] ?? null,
=======
                WHERE a.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function salvarAgendamento($data) {
        $agendamentoData = [
            'paciente_id' => $data['paciente_id'],
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function atualizarStatusAgendamento($id, $status)
    {
        return $this->db->update('master_agendamentos', ['status' => $status], 'id = ?', [$id]);
    }

    public function deletarAgendamento($id)
    {
=======
    public function atualizarStatusAgendamento($id, $status) {
        return $this->db->update('master_agendamentos', ['status' => $status], 'id = ?', [$id]);
    }

    public function deletarAgendamento($id) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        return $this->db->delete('master_agendamentos', 'id = ?', [$id]);
    }

    // Salvar procedimentos do agendamento (multiplos)
<<<<<<< HEAD
    public function salvarAgendamentoProcedimentos($agendamentoId, $procedimentos)
    {
=======
    public function salvarAgendamentoProcedimentos($agendamentoId, $procedimentos) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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
<<<<<<< HEAD
    public function getAgendamentoProcedimentos($agendamentoId)
    {
=======
    public function getAgendamentoProcedimentos($agendamentoId) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        $sql = "SELECT ap.*, p.procedimento, p.duracao_minutos
                FROM master_agendamento_procedimentos ap
                LEFT JOIN master_procedimentos p ON ap.procedimento_id = p.id
                WHERE ap.agendamento_id = ?";
        return $this->db->fetchAll($sql, [$agendamentoId]);
    }

    // ==================== DASHBOARD / ESTATISTICAS ====================

<<<<<<< HEAD
    public function getEstatisticasHoje()
    {
=======
    public function getEstatisticasHoje() {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        $hoje = date('Y-m-d');

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'agendado' THEN 1 ELSE 0 END) as agendados,
                    SUM(CASE WHEN status = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
                    SUM(CASE WHEN status = 'realizado' THEN 1 ELSE 0 END) as realizados,
                    SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(CASE WHEN status = 'faltou' THEN 1 ELSE 0 END) as faltaram,
<<<<<<< HEAD
                    COALESCE(SUM(CASE WHEN status = 'realizado' AND convenio_id IS NULL THEN valor ELSE 0 END), 0) as faturamento,
                    COALESCE(SUM(CASE WHEN status = 'realizado' AND convenio_id IS NOT NULL THEN valor ELSE 0 END), 0) as faturamento_convenio
=======
                    COALESCE(SUM(CASE WHEN status = 'realizado' THEN valor ELSE 0 END), 0) as faturamento
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                FROM master_agendamentos
                WHERE data_consulta = ?";

        return $this->db->fetchOne($sql, [$hoje]);
    }

<<<<<<< HEAD
    public function getEstatisticasMes($mes = null, $ano = null)
    {
=======
    public function getEstatisticasMes($mes = null, $ano = null) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        $mes = $mes ?? date('m');
        $ano = $ano ?? date('Y');

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'realizado' THEN 1 ELSE 0 END) as realizados,
                    SUM(CASE WHEN status = 'cancelado' OR status = 'faltou' THEN 1 ELSE 0 END) as perdidos,
<<<<<<< HEAD
                    COALESCE(SUM(CASE WHEN status = 'realizado' AND convenio_id IS NULL THEN valor ELSE 0 END), 0) as faturamento,
                    COALESCE(SUM(CASE WHEN status = 'realizado' AND convenio_id IS NOT NULL THEN valor ELSE 0 END), 0) as faturamento_convenio
=======
                    COALESCE(SUM(CASE WHEN status = 'realizado' THEN valor ELSE 0 END), 0) as faturamento
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                FROM master_agendamentos
                WHERE MONTH(data_consulta) = ? AND YEAR(data_consulta) = ?";

        return $this->db->fetchOne($sql, [$mes, $ano]);
    }

<<<<<<< HEAD
    public function getAgendamentosProximos($limite = 10)
    {
=======
    public function getAgendamentosProximos($limite = 10) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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

<<<<<<< HEAD
    public function getHorariosOcupados($data, $profissionalId = null)
    {
=======
    public function getHorariosOcupados($data, $profissionalId = null) {
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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
