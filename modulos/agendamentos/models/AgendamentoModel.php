<?php

/**
 * Classe AgendamentoModel - Gerencia operações relacionadas aos agendamentos
 */
class AgendamentoModel extends Model
{

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = 'agendamentos';
    }

    /**
     * Valida os dados do agendamento
     * @param array $data
     * @return array [success, message, errors]
     */
    public function validate($data)
    {
        $errors = [];

        // No método validate(), adicione:
        if (empty($data['procedimento_id'])) {
            $errors['procedimento_id'] = 'O procedimento é obrigatório';
        }
        // Validações básicas
        if (empty($data['paciente_id'])) {
            $errors['paciente_id'] = 'O paciente é obrigatório';
        }

        if (empty($data['clinica_id'])) {
            $errors['clinica_id'] = 'A clínica é obrigatória';
        }

        if (empty($data['especialidade_id'])) {
            $errors['especialidade_id'] = 'A especialidade é obrigatória';
        }

        if (empty($data['data_consulta'])) {
            $errors['data_consulta'] = 'A data da consulta é obrigatória';
        } else if (!$this->validateDate($data['data_consulta'])) {
            $errors['data_consulta'] = 'Data da consulta inválida';
        }

        if (empty($data['hora_consulta'])) {
            $errors['hora_consulta'] = 'A hora da consulta é obrigatória';
        } else if (!$this->validateTime($data['hora_consulta'])) {
            $errors['hora_consulta'] = 'Hora da consulta inválida';
        }

        // Verificar se já existe um agendamento para o mesmo paciente, na mesma data e horário (exceto para atualizações)
        if (!empty($data['paciente_id']) && !empty($data['data_consulta']) && !empty($data['hora_consulta'])) {
            $datetime = $this->formatDateForDatabase($data['data_consulta']) . ' ' . $data['hora_consulta'];

            // Formatando a data para o banco
            $conditions = [
                'paciente_id' => $data['paciente_id'],
                'data_consulta' => $this->formatDateForDatabase($data['data_consulta']),
                'hora_consulta' => $data['hora_consulta']
            ];

            if (isset($data['id']) && !empty($data['id'])) {
                $existingAgendamento = $this->getOneWhere($conditions);
                if ($existingAgendamento && $existingAgendamento['id'] != $data['id']) {
                    $errors['data_consulta'] = 'Já existe um agendamento para este paciente nesta data e horário';
                    $errors['hora_consulta'] = 'Já existe um agendamento para este paciente nesta data e horário';
                }
            } else {
                if ($this->exists($conditions)) {
                    $errors['data_consulta'] = 'Já existe um agendamento para este paciente nesta data e horário';
                    $errors['hora_consulta'] = 'Já existe um agendamento para este paciente nesta data e horário';
                }
            }
        }

        $success = empty($errors);
        $message = $success ? 'Dados válidos' : 'Existem erros nos dados fornecidos';

        return [
            'success' => $success,
            'message' => $message,
            'errors' => $errors
        ];
    }

    /**
     * Formata os dados antes de salvar
     * @param array $data
     * @return array
     */
    public function formatData($data)
    {
        // Formata a data da consulta para o formato do banco (yyyy-mm-dd)
        if (isset($data['data_consulta'])) {
            $data['data_consulta'] = $this->formatDateForDatabase($data['data_consulta']);
        }

        return $data;
    }

    // /**
    //  * Salva os dados do agendamento após validação e formatação
    //  * @param array $data
    //  * @return array [success, message, id]
    //  */
    // public function saveAgendamento($data) {
    //     // Validar dados
    //     $validation = $this->validate($data);
    //     if (!$validation['success']) {
    //         return [
    //             'success' => false,
    //             'message' => $validation['message'],
    //             'errors' => $validation['errors'],
    //             'id' => null
    //         ];
    //     }

    //     // Formatar dados
    //     $data = $this->formatData($data);

    //     // Salvar no banco
    //     try {
    //         $id = $this->save($data);
    //         return [
    //             'success' => true,
    //             'message' => 'Agendamento salvo com sucesso',
    //             'id' => $id
    //         ];
    //     } catch (Exception $e) {
    //         return [
    //             'success' => false,
    //             'message' => 'Erro ao salvar agendamento: ' . $e->getMessage(),
    //             'errors' => ['database' => $e->getMessage()],
    //             'id' => null
    //         ];
    //     }
    // }

    public function saveAgendamento($data)
    {
        // Validar dados
        $validation = $this->validate($data);
        if (!$validation['success']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'],
                'id' => null
            ];
        }

        // Formatar dados
        $data = $this->formatData($data);

        // Salvar no banco
        try {
            $id = $this->save($data);

            // Criar notificação para novo agendamento
            if ($id && (empty($data['id']) || $data['id'] == 0)) {
                $agendamento = $this->getAgendamentoCompleto($id);
                $this->criarNotificacaoNovoAgendamento($agendamento);
            }

            return [
                'success' => true,
                'message' => 'Agendamento salvo com sucesso',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao salvar agendamento: ' . $e->getMessage(),
                'errors' => ['database' => $e->getMessage()],
                'id' => null
            ];
        }
    }


    /**
     * Busca agendamentos com filtros
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchAgendamentos($filters = [], $limit = null, $offset = null)
    {
        $sql = "
            SELECT a.*, 
                   p.nome as paciente_nome, 
                   c.nome as clinica_nome, 
                   e.nome as especialidade_nome,
                   CONCAT(a.data_consulta, ' ', a.hora_consulta) as data_hora,
                   a.status_agendamento as status
            FROM {$this->table} a
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            LEFT JOIN especialidades e ON a.especialidade_id = e.id
            WHERE 1=1
        ";

        $params = [];

        // Filtro por paciente
        if (!empty($filters['paciente_id'])) {
            $sql .= " AND a.paciente_id = ?";
            $params[] = $filters['paciente_id'];
        }

        // Filtro por nome do paciente
        if (!empty($filters['paciente_nome'])) {
            $sql .= " AND p.nome LIKE ?";
            $params[] = "%" . $filters['paciente_nome'] . "%";
        }

        // Filtro por clínica
        if (!empty($filters['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filters['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filters['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filters['especialidade_id'];
        }

        // Filtro por data da consulta
        if (!empty($filters['data_consulta'])) {
            $sql .= " AND a.data_consulta = ?";
            $params[] = $this->formatDateForDatabase($filters['data_consulta']);
        }

        // Filtro por período
        if (!empty($filters['data_inicio']) && !empty($filters['data_fim'])) {
            $sql .= " AND a.data_consulta BETWEEN ? AND ?";
            $params[] = $this->formatDateForDatabase($filters['data_inicio']);
            $params[] = $this->formatDateForDatabase($filters['data_fim']);
        } else if (!empty($filters['data_inicio'])) {
            $sql .= " AND a.data_consulta >= ?";
            $params[] = $this->formatDateForDatabase($filters['data_inicio']);
        } else if (!empty($filters['data_fim'])) {
            $sql .= " AND a.data_consulta <= ?";
            $params[] = $this->formatDateForDatabase($filters['data_fim']);
        }

        // Filtro por status do agendamento
        if (!empty($filters['status_agendamento'])) {
            $sql .= " AND a.status_agendamento = ?";
            $params[] = $filters['status_agendamento'];
        }

        // Ordenação padrão: por data e hora da consulta
        $sql .= " ORDER BY a.data_consulta, a.hora_consulta";

        // Paginação
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $results = $this->db->fetchAll($sql, $params);

        // Formata as datas para exibição
        foreach ($results as &$result) {
            $result['data_consulta_formatada'] = $this->formatDateForDisplay($result['data_consulta']);
        }

        return $results;
    }


    /**
     * Obtém a contagem de agendamentos por especialidade
     * @return array
     */
    public function getAgendamentosPorEspecialidade()
    {
        $sql = "
        SELECT e.nome as especialidade, COUNT(a.id) as total
        FROM {$this->table} a
        JOIN especialidades e ON a.especialidade_id = e.id
        GROUP BY a.especialidade_id
        ORDER BY total DESC
    ";

        $results = $this->db->fetchAll($sql);

        // Formata os resultados para o formato esperado pelo gráfico
        $especialidades = [];
        $totais = [];
        $cores = [
            '#4e73df', // Azul
            '#1cc88a', // Verde
            '#36b9cc', // Ciano
            '#f6c23e', // Amarelo
            '#e74a3b', // Vermelho
            '#5a5c69'  // Cinza
        ];

        $i = 0;
        foreach ($results as $result) {
            $especialidades[] = $result['especialidade'];
            $totais[] = (int)$result['total'];
            $i++;
        }

        // Se não houver dados, retorna um array vazio
        if (empty($especialidades)) {
            return [
                'especialidades' => [],
                'totais' => [],
                'cores' => [],
                'hoverCores' => []
            ];
        }

        // Limita a 5 especialidades e agrupa o resto como "Outras"
        if (count($especialidades) > 5) {
            $outrasTotal = 0;
            for ($j = 5; $j < count($totais); $j++) {
                $outrasTotal += $totais[$j];
            }

            $especialidades = array_slice($especialidades, 0, 5);
            $totais = array_slice($totais, 0, 5);

            $especialidades[] = 'Outras';
            $totais[] = $outrasTotal;
        }

        // Cores para o gráfico
        $coresGrafico = array_slice($cores, 0, count($especialidades));
        $hoverCores = [
            '#2e59d9', // Azul hover
            '#17a673', // Verde hover
            '#2c9faf', // Ciano hover
            '#dda20a', // Amarelo hover
            '#be2617', // Vermelho hover
            '#4e5055'  // Cinza hover
        ];
        $hoverCoresGrafico = array_slice($hoverCores, 0, count($especialidades));

        return [
            'especialidades' => $especialidades,
            'totais' => $totais,
            'cores' => $coresGrafico,
            'hoverCores' => $hoverCoresGrafico
        ];
    }

    /**
     * Cria uma notificação de novo agendamento
     * @param array $agendamento
     */
    public function criarNotificacaoNovoAgendamento($agendamento)
    {
        require_once MODULES_PATH . '/sistema/models/NotificacaoModel.php';
        $notificacaoModel = new NotificacaoModel();

        $notificacao = [
            'tipo' => 'agendamento',
            'icone' => 'calendar-check',
            'cor' => 'primary',
            'titulo' => 'Novo agendamento criado',
            'mensagem' => "Agendamento para o paciente {$agendamento['paciente_nome']} foi criado para {$agendamento['data_consulta_formatada']} às {$agendamento['hora_consulta']}",
            'link' => "index.php?module=agendamentos&action=view&id={$agendamento['id']}"
        ];

        $notificacaoModel->criarNotificacao($notificacao);
    }

    /**
     * Cria uma notificação de cancelamento de agendamento
     * @param array $agendamento
     */
    public function criarNotificacaoCancelamentoAgendamento($agendamento)
    {
        require_once MODULES_PATH . '/sistema/models/NotificacaoModel.php';
        $notificacaoModel = new NotificacaoModel();

        $notificacao = [
            'tipo' => 'alerta',
            'icone' => 'exclamation-triangle',
            'cor' => 'warning',
            'titulo' => 'Agendamento cancelado',
            'mensagem' => "Agendamento para o paciente {$agendamento['paciente_nome']} foi cancelado",
            'link' => "index.php?module=agendamentos&action=view&id={$agendamento['id']}"
        ];

        $notificacaoModel->criarNotificacao($notificacao);
    }


    /**
     * Conta o número de agendamentos com os filtros aplicados
     * @param array $filters
     * @return int
     */
    public function countAgendamentos($filters = [])
    {
        $sql = "
            SELECT COUNT(*) as total
            FROM {$this->table} a
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            LEFT JOIN especialidades e ON a.especialidade_id = e.id
            WHERE 1=1
        ";

        $params = [];

        // Filtro por paciente
        if (!empty($filters['paciente_id'])) {
            $sql .= " AND a.paciente_id = ?";
            $params[] = $filters['paciente_id'];
        }

        // Filtro por nome do paciente
        if (!empty($filters['paciente_nome'])) {
            $sql .= " AND p.nome LIKE ?";
            $params[] = "%" . $filters['paciente_nome'] . "%";
        }

        // Filtro por clínica
        if (!empty($filters['clinica_id'])) {
            $sql .= " AND a.clinica_id = ?";
            $params[] = $filters['clinica_id'];
        }

        // Filtro por especialidade
        if (!empty($filters['especialidade_id'])) {
            $sql .= " AND a.especialidade_id = ?";
            $params[] = $filters['especialidade_id'];
        }

        // Filtro por data da consulta
        if (!empty($filters['data_consulta'])) {
            $sql .= " AND a.data_consulta = ?";
            $params[] = $this->formatDateForDatabase($filters['data_consulta']);
        }

        // Filtro por período
        if (!empty($filters['data_inicio']) && !empty($filters['data_fim'])) {
            $sql .= " AND a.data_consulta BETWEEN ? AND ?";
            $params[] = $this->formatDateForDatabase($filters['data_inicio']);
            $params[] = $this->formatDateForDatabase($filters['data_fim']);
        } else if (!empty($filters['data_inicio'])) {
            $sql .= " AND a.data_consulta >= ?";
            $params[] = $this->formatDateForDatabase($filters['data_inicio']);
        } else if (!empty($filters['data_fim'])) {
            $sql .= " AND a.data_consulta <= ?";
            $params[] = $this->formatDateForDatabase($filters['data_fim']);
        }

        // Filtro por status do agendamento
        if (!empty($filters['status_agendamento'])) {
            $sql .= " AND a.status_agendamento = ?";
            $params[] = $filters['status_agendamento'];
        }

        $result = $this->db->fetchOne($sql, $params);
        return (int) $result['total'];
    }

    /**
     * Obtém um agendamento com informações relacionadas
     * @param int $id
     * @return array|null
     */
    public function getAgendamentoCompleto($id)
    {
        $sql = "
            SELECT a.*, 
                   p.nome as paciente_nome, p.telefone_fixo as paciente_telefone_fixo, p.celular as paciente_celular,
                   c.nome as clinica_nome, c.endereco as clinica_endereco, c.telefone as clinica_telefone,
                   e.nome as especialidade_nome
            FROM {$this->table} a
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            LEFT JOIN especialidades e ON a.especialidade_id = e.id
            WHERE a.id = ?
        ";

        $agendamento = $this->db->fetchOne($sql, [$id]);

        if (!$agendamento) {
            return null;
        }

        // Formata as datas para exibição
        $agendamento['data_consulta_formatada'] = $this->formatDateForDisplay($agendamento['data_consulta']);

        // Formata a data de agendamento para exibição
        if (!empty($agendamento['data_agendamento'])) {
            $agendamento['data_agendamento_formatada'] = $this->formatDateForDisplay($agendamento['data_agendamento'], true);
        }

        // Formata a data de última atualização para exibição
        if (!empty($agendamento['ultima_atualizacao'])) {
            $agendamento['ultima_atualizacao_formatada'] = $this->formatDateForDisplay($agendamento['ultima_atualizacao'], true);
        }

        return $agendamento;
    }

    /**
     * Verifica se um horário está disponível para agendamento
     * @param string $data
     * @param string $hora
     * @param int $clinicaId
     * @param int $especialidadeId
     * @param int $agendamentoIdIgnorar ID do agendamento a ser ignorado na validação (para edições)
     * @return bool
     */
    public function isHorarioDisponivel($data, $hora, $clinicaId, $especialidadeId, $agendamentoIdIgnorar = null)
    {
        $sql = "
            SELECT COUNT(*) as total
            FROM {$this->table}
            WHERE clinica_id = ?
            AND especialidade_id = ?
            AND data_consulta = ?
            AND hora_consulta = ?
        ";

        $params = [
            $clinicaId,
            $especialidadeId,
            $this->formatDateForDatabase($data),
            $hora
        ];

        // Ignora o agendamento atual (para edições)
        if ($agendamentoIdIgnorar) {
            $sql .= " AND id != ?";
            $params[] = $agendamentoIdIgnorar;
        }

        $result = $this->db->fetchOne($sql, $params);
        return (int) $result['total'] === 0;
    }

    /**
     * Obtém os horários disponíveis para uma data, clínica e especialidade
     * @param string $data
     * @param int $clinicaId
     * @param int $especialidadeId
     * @return array
     */
    public function getHorariosDisponiveis($data, $clinicaId, $especialidadeId)
    {
        // Horários padrão de funcionamento
        $horariosPadrao = [
            '08:00:00',
            '08:30:00',
            '09:00:00',
            '09:30:00',
            '10:00:00',
            '10:30:00',
            '11:00:00',
            '11:30:00',
            '13:00:00',
            '13:30:00',
            '14:00:00',
            '14:30:00',
            '15:00:00',
            '15:30:00',
            '16:00:00',
            '16:30:00',
            '17:00:00',
            '17:30:00'
        ];

        // Busca os horários já agendados
        $sql = "
            SELECT hora_consulta
            FROM {$this->table}
            WHERE clinica_id = ?
            AND especialidade_id = ?
            AND data_consulta = ?
        ";

        $params = [
            $clinicaId,
            $especialidadeId,
            $this->formatDateForDatabase($data)
        ];

        $horariosOcupados = [];
        $results = $this->db->fetchAll($sql, $params);

        foreach ($results as $result) {
            $horariosOcupados[] = $result['hora_consulta'];
        }

        // Filtra apenas os horários disponíveis
        $horariosDisponiveis = array_diff($horariosPadrao, $horariosOcupados);

        // Converte para array simples
        return array_values($horariosDisponiveis);
    }

    /**
     * Atualiza o status de um agendamento
     * @param int $id
     * @param string $status
     * @return bool
     */
    // public function atualizarStatus($id, $status) {
    //     $statusValidos = ['agendado', 'confirmado', 'realizado', 'cancelado'];

    //     if (!in_array($status, $statusValidos)) {
    //         return false;
    //     }

    //     return $this->update($id, ['status_agendamento' => $status]) > 0;
    // }

    public function atualizarStatus($id, $status)
    {
        $statusValidos = ['agendado', 'confirmado', 'realizado', 'cancelado'];

        if (!in_array($status, $statusValidos)) {
            return false;
        }

        $success = $this->update($id, ['status_agendamento' => $status]) > 0;

        // Se foi cancelado, criar notificação
        if ($success && $status === 'cancelado') {
            $agendamento = $this->getAgendamentoCompleto($id);
            $this->criarNotificacaoCancelamentoAgendamento($agendamento);
        }

        return $success;
    }


    /**
     * Formata a data do formato brasileiro para o formato do banco
     * @param string $date
     * @return string
     */
    public function formatDateForDatabase($date)
    {
        if (empty($date)) {
            return null;
        }

        // Converte do formato brasileiro para o formato do MySQL
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        return $date;
    }

    /**
     * Formata a data para exibição
     * @param string $date
     * @param bool $withTime
     * @return string
     */
    public function formatDateForDisplay($date, $withTime = false)
    {
        if (empty($date)) {
            return '';
        }

        // Data no formato MySQL (yyyy-mm-dd)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $matches)) {
            if ($withTime && strlen($date) > 10) {
                $datetime = new DateTime($date);
                return $datetime->format('d/m/Y H:i:s');
            } else {
                return "{$matches[3]}/{$matches[2]}/{$matches[1]}";
            }
        }

        return $date;
    }

    /**
     * Valida uma data
     * @param string $date
     * @return bool
     */
    private function validateDate($date)
    {
        if (empty($date)) {
            return false;
        }

        // Verifica formato brasileiro (dd/mm/yyyy)
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];

            return checkdate($month, $day, $year);
        }

        // Verifica formato ISO (yyyy-mm-dd)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            $day = (int) $matches[3];

            return checkdate($month, $day, $year);
        }

        return false;
    }

    /**
     * Valida um horário
     * @param string $time
     * @return bool
     */
    private function validateTime($time)
    {
        if (empty($time)) {
            return false;
        }

        // Verifica o formato de hora (hh:mm:ss)
        if (preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $time)) {
            return true;
        }

        // Verifica o formato de hora sem segundos (hh:mm)
        if (preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time)) {
            return true;
        }

        return false;
    }
}
