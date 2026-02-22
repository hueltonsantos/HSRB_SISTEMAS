<?php

class CaixaModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'caixa_lancamentos';
    }

    // =============================================
    // LANÇAMENTOS
    // =============================================

    public function listarLancamentos($filtros = [], $limit = null, $offset = null)
    {
        $sql = "
            SELECT l.*,
                   p.nome as paciente_nome,
                   c.nome as clinica_nome,
                   u.nome as usuario_nome
            FROM caixa_lancamentos l
            LEFT JOIN pacientes p ON l.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON l.clinica_id = c.id
            LEFT JOIN usuarios u ON l.usuario_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $sql .= " AND l.data BETWEEN ? AND ?";
            $params[] = $this->formatDateForDB($filtros['data_inicio']);
            $params[] = $this->formatDateForDB($filtros['data_fim']);
        } elseif (!empty($filtros['data'])) {
            $sql .= " AND l.data = ?";
            $params[] = $this->formatDateForDB($filtros['data']);
        }

        if (!empty($filtros['tipo'])) {
            $sql .= " AND l.tipo = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['forma_pagamento'])) {
            $sql .= " AND l.forma_pagamento = ?";
            $params[] = $filtros['forma_pagamento'];
        }

        if (!empty($filtros['fechamento_id'])) {
            $sql .= " AND l.fechamento_id = ?";
            $params[] = $filtros['fechamento_id'];
        }

        $sql .= " ORDER BY l.data DESC, l.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function countLancamentos($filtros = [])
    {
        $sql = "SELECT COUNT(*) as total FROM caixa_lancamentos l WHERE 1=1";
        $params = [];

        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $sql .= " AND l.data BETWEEN ? AND ?";
            $params[] = $this->formatDateForDB($filtros['data_inicio']);
            $params[] = $this->formatDateForDB($filtros['data_fim']);
        } elseif (!empty($filtros['data'])) {
            $sql .= " AND l.data = ?";
            $params[] = $this->formatDateForDB($filtros['data']);
        }

        if (!empty($filtros['tipo'])) {
            $sql .= " AND l.tipo = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['forma_pagamento'])) {
            $sql .= " AND l.forma_pagamento = ?";
            $params[] = $filtros['forma_pagamento'];
        }

        if (!empty($filtros['fechamento_id'])) {
            $sql .= " AND l.fechamento_id = ?";
            $params[] = $filtros['fechamento_id'];
        }

        $result = $this->db->fetchOne($sql, $params);
        return (int)$result['total'];
    }

    public function getLancamentoCompleto($id)
    {
        $sql = "
            SELECT l.*,
                   p.nome as paciente_nome, p.cpf as paciente_cpf, p.celular as paciente_celular,
                   c.nome as clinica_nome,
                   u.nome as usuario_nome,
                   a.data_consulta, a.hora_consulta, a.especialidade_id,
                   e.nome as especialidade_nome
            FROM caixa_lancamentos l
            LEFT JOIN pacientes p ON l.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON l.clinica_id = c.id
            LEFT JOIN usuarios u ON l.usuario_id = u.id
            LEFT JOIN agendamentos a ON l.agendamento_id = a.id
            LEFT JOIN especialidades e ON a.especialidade_id = e.id
            WHERE l.id = ?
        ";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function salvarLancamento($data)
    {
        $errors = [];

        if (empty($data['descricao'])) {
            $errors['descricao'] = 'A descrição é obrigatória';
        }
        if (empty($data['valor']) || $data['valor'] <= 0) {
            $errors['valor'] = 'O valor deve ser maior que zero';
        }
        if (empty($data['tipo'])) {
            $errors['tipo'] = 'O tipo é obrigatório';
        }
        if (empty($data['forma_pagamento'])) {
            $errors['forma_pagamento'] = 'A forma de pagamento é obrigatória';
        }
        if (empty($data['data'])) {
            $errors['data'] = 'A data é obrigatória';
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => 'Erros de validação', 'errors' => $errors];
        }

        $data['data'] = $this->formatDateForDB($data['data']);

        // Vincular ao caixa aberto do dia se existir
        if (empty($data['fechamento_id'])) {
            $caixaAberto = $this->getCaixaAberto();
            if ($caixaAberto) {
                $data['fechamento_id'] = $caixaAberto['id'];
            }
        }

        try {
            $id = $this->save($data);
            return ['success' => true, 'message' => 'Lançamento salvo com sucesso', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao salvar: ' . $e->getMessage(), 'errors' => []];
        }
    }

    public function getTotaisPorFormaPagamento($data = null)
    {
        $sql = "
            SELECT forma_pagamento, tipo,
                   SUM(valor) as total
            FROM caixa_lancamentos
            WHERE 1=1
        ";
        $params = [];

        if ($data) {
            $sql .= " AND data = ?";
            $params[] = $this->formatDateForDB($data);
        }

        $sql .= " GROUP BY forma_pagamento, tipo";

        return $this->db->fetchAll($sql, $params);
    }

    public function getResumoDia($data = null)
    {
        if (!$data) {
            $data = date('Y-m-d');
        } else {
            $data = $this->formatDateForDB($data);
        }

        $sql = "
            SELECT
                COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END), 0) as total_saidas,
                COUNT(*) as total_lancamentos
            FROM caixa_lancamentos
            WHERE data = ?
        ";

        return $this->db->fetchOne($sql, [$data]);
    }

    // =============================================
    // FECHAMENTO DE CAIXA
    // =============================================

    public function getCaixaAberto()
    {
        return $this->db->fetchOne(
            "SELECT * FROM caixa_fechamentos WHERE status = 'aberto' ORDER BY data_abertura DESC LIMIT 1"
        );
    }

    public function abrirCaixa($data)
    {
        // Verifica se já existe caixa aberto
        $caixaAberto = $this->getCaixaAberto();
        if ($caixaAberto) {
            return ['success' => false, 'message' => 'Já existe um caixa aberto. Feche-o antes de abrir um novo.'];
        }

        $saldoInicial = isset($data['saldo_inicial']) ? (float)$data['saldo_inicial'] : 0;

        try {
            $id = $this->db->insert('caixa_fechamentos', [
                'data' => date('Y-m-d'),
                'saldo_inicial' => $saldoInicial,
                'status' => 'aberto',
                'usuario_abertura_id' => $_SESSION['usuario_id']
            ]);
            return ['success' => true, 'message' => 'Caixa aberto com sucesso', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao abrir caixa: ' . $e->getMessage()];
        }
    }

    public function fecharCaixa($id, $observacoes = '')
    {
        $caixa = $this->db->fetchOne("SELECT * FROM caixa_fechamentos WHERE id = ?", [$id]);
        if (!$caixa || $caixa['status'] !== 'aberto') {
            return ['success' => false, 'message' => 'Caixa não encontrado ou já fechado'];
        }

        // Calcula totais dos lançamentos vinculados
        $totais = $this->db->fetchOne("
            SELECT
                COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END), 0) as total_saidas
            FROM caixa_lancamentos
            WHERE fechamento_id = ?
        ", [$id]);

        $totalEntradas = (float)$totais['total_entradas'];
        $totalSaidas = (float)$totais['total_saidas'];
        $saldoFinal = (float)$caixa['saldo_inicial'] + $totalEntradas - $totalSaidas;

        try {
            $this->db->update('caixa_fechamentos', [
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'saldo_final' => $saldoFinal,
                'observacoes' => $observacoes,
                'status' => 'fechado',
                'usuario_fechamento_id' => $_SESSION['usuario_id'],
                'data_fechamento' => date('Y-m-d H:i:s')
            ], 'id = ?', [$id]);

            return ['success' => true, 'message' => 'Caixa fechado com sucesso', 'saldo_final' => $saldoFinal];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao fechar caixa: ' . $e->getMessage()];
        }
    }

    public function getHistoricoFechamentos($limit = 30, $offset = 0)
    {
        $sql = "
            SELECT f.*,
                   ua.nome as usuario_abertura_nome,
                   uf.nome as usuario_fechamento_nome
            FROM caixa_fechamentos f
            LEFT JOIN usuarios ua ON f.usuario_abertura_id = ua.id
            LEFT JOIN usuarios uf ON f.usuario_fechamento_id = uf.id
            ORDER BY f.data DESC, f.data_abertura DESC
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        return $this->db->fetchAll($sql);
    }

    public function countFechamentos()
    {
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM caixa_fechamentos");
        return (int)$result['total'];
    }

    // =============================================
    // REPASSES
    // =============================================

    public function listarRepasses($filtros = [], $limit = null, $offset = null)
    {
        $sql = "
            SELECT r.*,
                   c.nome as clinica_nome,
                   u.nome as usuario_nome
            FROM repasses r
            LEFT JOIN clinicas_parceiras c ON r.clinica_id = c.id
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND r.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        if (!empty($filtros['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filtros['status'];
        }

        if (!empty($filtros['periodo_inicio'])) {
            $sql .= " AND r.periodo_inicio >= ?";
            $params[] = $this->formatDateForDB($filtros['periodo_inicio']);
        }

        if (!empty($filtros['periodo_fim'])) {
            $sql .= " AND r.periodo_fim <= ?";
            $params[] = $this->formatDateForDB($filtros['periodo_fim']);
        }

        $sql .= " ORDER BY r.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function countRepasses($filtros = [])
    {
        $sql = "SELECT COUNT(*) as total FROM repasses r WHERE 1=1";
        $params = [];

        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND r.clinica_id = ?";
            $params[] = $filtros['clinica_id'];
        }

        if (!empty($filtros['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filtros['status'];
        }

        $result = $this->db->fetchOne($sql, $params);
        return (int)$result['total'];
    }

    public function gerarRepasse($clinicaId, $periodoInicio, $periodoFim)
    {
        $periodoInicio = $this->formatDateForDB($periodoInicio);
        $periodoFim = $this->formatDateForDB($periodoFim);

        // Busca agendamentos realizados no período para a clínica
        $sql = "
            SELECT a.id as agendamento_id, ap.procedimento_id,
                   ap.valor as valor_procedimento,
                   vp.valor_repasse
            FROM agendamentos a
            INNER JOIN agendamento_procedimentos ap ON a.id = ap.agendamento_id
            INNER JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
            WHERE a.clinica_id = ?
              AND a.status_agendamento = 'realizado'
              AND a.data_consulta BETWEEN ? AND ?
              AND a.id NOT IN (
                  SELECT ri.agendamento_id FROM repasse_itens ri
              )
        ";

        $itens = $this->db->fetchAll($sql, [$clinicaId, $periodoInicio, $periodoFim]);

        if (empty($itens)) {
            return ['success' => false, 'message' => 'Nenhum agendamento realizado encontrado no período para esta clínica (ou já foram incluídos em repasses anteriores).'];
        }

        $valorTotal = 0;
        foreach ($itens as $item) {
            $valorTotal += (float)$item['valor_repasse'];
        }

        try {
            $this->db->beginTransaction();

            $repasseId = $this->db->insert('repasses', [
                'clinica_id' => $clinicaId,
                'periodo_inicio' => $periodoInicio,
                'periodo_fim' => $periodoFim,
                'valor_total' => $valorTotal,
                'status' => 'pendente',
                'usuario_id' => $_SESSION['usuario_id']
            ]);

            foreach ($itens as $item) {
                $this->db->insert('repasse_itens', [
                    'repasse_id' => $repasseId,
                    'agendamento_id' => $item['agendamento_id'],
                    'procedimento_id' => $item['procedimento_id'],
                    'valor_procedimento' => $item['valor_procedimento'],
                    'valor_repasse' => $item['valor_repasse']
                ]);
            }

            $this->db->commit();

            return ['success' => true, 'message' => 'Repasse gerado com sucesso', 'id' => $repasseId, 'valor_total' => $valorTotal, 'qtd_itens' => count($itens)];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Erro ao gerar repasse: ' . $e->getMessage()];
        }
    }

    public function getRepasseCompleto($id)
    {
        $repasse = $this->db->fetchOne("
            SELECT r.*, c.nome as clinica_nome, c.cnpj as clinica_cnpj, u.nome as usuario_nome
            FROM repasses r
            LEFT JOIN clinicas_parceiras c ON r.clinica_id = c.id
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            WHERE r.id = ?
        ", [$id]);

        if (!$repasse) return null;

        $repasse['itens'] = $this->db->fetchAll("
            SELECT ri.*,
                   p.nome as paciente_nome,
                   vp.procedimento as procedimento_nome,
                   a.data_consulta
            FROM repasse_itens ri
            INNER JOIN agendamentos a ON ri.agendamento_id = a.id
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            LEFT JOIN valores_procedimentos vp ON ri.procedimento_id = vp.id
            WHERE ri.repasse_id = ?
            ORDER BY a.data_consulta
        ", [$id]);

        return $repasse;
    }

    public function registrarPagamentoRepasse($id, $data)
    {
        $repasse = $this->db->fetchOne("SELECT * FROM repasses WHERE id = ?", [$id]);
        if (!$repasse) {
            return ['success' => false, 'message' => 'Repasse não encontrado'];
        }

        $valorPago = (float)$data['valor_pago'];
        $novoTotalPago = (float)$repasse['valor_pago'] + $valorPago;
        $status = $novoTotalPago >= (float)$repasse['valor_total'] ? 'pago' : 'parcial';

        try {
            $this->db->update('repasses', [
                'valor_pago' => $novoTotalPago,
                'status' => $status,
                'data_pagamento' => date('Y-m-d'),
                'observacoes' => $data['observacoes'] ?? $repasse['observacoes']
            ], 'id = ?', [$id]);

            return ['success' => true, 'message' => 'Pagamento registrado com sucesso', 'status' => $status];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao registrar pagamento: ' . $e->getMessage()];
        }
    }

    public function getClinicas()
    {
        return $this->db->fetchAll("SELECT id, nome FROM clinicas_parceiras WHERE status = 1 ORDER BY nome");
    }

    // =============================================
    // AGENDAMENTOS DO DIA (para lançar no caixa)
    // =============================================

    public function getAgendamentosDoDia($data = null)
    {
        if (!$data) {
            $data = date('Y-m-d');
        } else {
            $data = $this->formatDateForDB($data);
        }

        $sql = "
            SELECT a.id, a.data_consulta, a.hora_consulta, a.valor_total,
                   a.forma_pagamento, a.status_agendamento,
                   p.id as paciente_id, p.nome as paciente_nome,
                   c.id as clinica_id, c.nome as clinica_nome,
                   e.nome as especialidade_nome,
                   GROUP_CONCAT(vp.procedimento SEPARATOR ', ') as procedimentos,
                   (SELECT COUNT(*) FROM caixa_lancamentos cl WHERE cl.agendamento_id = a.id) as ja_lancado
            FROM agendamentos a
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            LEFT JOIN especialidades e ON a.especialidade_id = e.id
            LEFT JOIN agendamento_procedimentos ap ON a.id = ap.agendamento_id
            LEFT JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
            WHERE a.data_consulta = ?
            GROUP BY a.id
            ORDER BY a.hora_consulta ASC
        ";

        return $this->db->fetchAll($sql, [$data]);
    }

    public function lancarAgendamentoNoCaixa($agendamentoId, $formaPagamento = null)
    {
        // Busca dados do agendamento
        $sql = "
            SELECT a.*, p.nome as paciente_nome, c.nome as clinica_nome
            FROM agendamentos a
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            WHERE a.id = ?
        ";
        $agendamento = $this->db->fetchOne($sql, [$agendamentoId]);

        if (!$agendamento) {
            return ['success' => false, 'message' => 'Agendamento não encontrado.'];
        }

        // Verifica se já foi lançado
        $jaLancado = $this->db->fetchOne(
            "SELECT id FROM caixa_lancamentos WHERE agendamento_id = ?",
            [$agendamentoId]
        );
        if ($jaLancado) {
            return ['success' => false, 'message' => 'Este agendamento já possui lançamento no caixa.'];
        }

        $valor = (float)$agendamento['valor_total'];
        if ($valor <= 0) {
            return ['success' => false, 'message' => 'O agendamento não possui valor. Verifique os procedimentos.'];
        }

        // Determina forma de pagamento
        $fp = $formaPagamento ?: $this->mapFormaPagamento($agendamento['forma_pagamento']);

        $data = [
            'data' => $agendamento['data_consulta'],
            'tipo' => 'entrada',
            'categoria' => 'Consulta',
            'descricao' => 'Atendimento - ' . $agendamento['paciente_nome'] . ' (' . $agendamento['clinica_nome'] . ')',
            'valor' => $valor,
            'forma_pagamento' => $fp,
            'agendamento_id' => $agendamentoId,
            'paciente_id' => $agendamento['paciente_id'],
            'clinica_id' => $agendamento['clinica_id'],
            'usuario_id' => $_SESSION['usuario_id']
        ];

        // Vincular ao caixa aberto
        $caixaAberto = $this->getCaixaAberto();
        if ($caixaAberto) {
            $data['fechamento_id'] = $caixaAberto['id'];
        }

        try {
            $id = $this->db->insert('caixa_lancamentos', $data);
            return ['success' => true, 'message' => 'Lançamento criado com sucesso!', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar lançamento: ' . $e->getMessage()];
        }
    }

    private function mapFormaPagamento($formaPgtoAgendamento)
    {
        $map = [
            'Dinheiro' => 'dinheiro',
            'PIX' => 'pix',
            'Cartão de Crédito' => 'cartao_credito',
            'Cartão de Débito' => 'cartao_debito',
            'Plano de Saúde' => 'convenio',
        ];
        return $map[$formaPgtoAgendamento] ?? 'dinheiro';
    }

    // =============================================
    // UTILIDADES
    // =============================================

    private function formatDateForDB($date)
    {
        if (empty($date)) return null;
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }
        return $date;
    }

    public function formatDateForDisplay($date)
    {
        if (empty($date)) return '';
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $m)) {
            return "{$m[3]}/{$m[2]}/{$m[1]}";
        }
        return $date;
    }

    public function getFormasPagamento()
    {
        return [
            'dinheiro' => 'Dinheiro',
            'pix' => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'convenio' => 'Convênio',
            'transferencia' => 'Transferência'
        ];
    }
}
