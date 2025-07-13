<?php

/**
 * Classe EspecialidadeModel - Gerencia operações relacionadas às especialidades
 */
class EspecialidadeModel extends Model
{

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = 'especialidades';
    }

    /**
     * Valida os dados da especialidade
     * @param array $data
     * @return array [success, message, errors]
     */
    public function validate($data)
    {
        $errors = [];

        // Validações básicas
        if (empty($data['nome'])) {
            $errors['nome'] = 'O nome da especialidade é obrigatório';
        } else {
            // Verifica se já existe uma especialidade com este nome (exceto para atualizações)
            $nome = trim($data['nome']);

            if (isset($data['id']) && !empty($data['id'])) {
                $existingEspecialidade = $this->getOneWhere(['nome' => $nome]);
                if ($existingEspecialidade && $existingEspecialidade['id'] != $data['id']) {
                    $errors['nome'] = 'Já existe uma especialidade com este nome';
                }
            } else {
                if ($this->exists(['nome' => $nome])) {
                    $errors['nome'] = 'Já existe uma especialidade com este nome';
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
     * Salva os dados da especialidade após validação
     * @param array $data
     * @return array [success, message, id]
     */
    public function saveEspecialidade($data)
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

        // Salvar no banco
        try {
            $id = $this->save($data);
            return [
                'success' => true,
                'message' => 'Especialidade salva com sucesso',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao salvar especialidade: ' . $e->getMessage(),
                'errors' => ['database' => $e->getMessage()],
                'id' => null
            ];
        }
    }

    /**
     * Busca especialidades com filtros
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchEspecialidades($filters = [], $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        // Filtro por nome
        if (!empty($filters['nome'])) {
            $sql .= " AND nome LIKE ?";
            $params[] = "%" . $filters['nome'] . "%";
        }

        // Filtro por status (ativo/inativo)
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        // Ordenação
        $sql .= " ORDER BY nome ASC";

        // Paginação
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Verifica se uma especialidade está sendo usada em clínicas
     * @param int $especialidadeId
     * @return bool
     */
    public function isUsedInClinicas($especialidadeId)
    {
        $sql = "SELECT COUNT(*) as total FROM especialidades_clinicas WHERE especialidade_id = ?";
        $result = $this->db->fetchOne($sql, [$especialidadeId]);
        return (int) $result['total'] > 0;
    }

    /**
     * Verifica se uma especialidade tem valores/procedimentos cadastrados
     * @param int $especialidadeId
     * @return bool
     */
    public function hasValoresProcedimentos($especialidadeId)
    {
        $sql = "SELECT COUNT(*) as total FROM valores_procedimentos WHERE especialidade_id = ?";
        $result = $this->db->fetchOne($sql, [$especialidadeId]);
        return (int) $result['total'] > 0;
    }

    /**
     * Obtém uma especialidade pelo ID
     * @param int $id
     * @return array
     */

    /**
     * Obtém todos os valores/procedimentos de uma especialidade
     * @param int $especialidadeId
     * @return array
     */
    public function getValoresProcedimentos($especialidadeId)
    {
        $sql = "SELECT * FROM valores_procedimentos WHERE especialidade_id = ? ORDER BY procedimento ASC";
        return $this->db->fetchAll($sql, [$especialidadeId]);
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
 * Sobrescreve o método getAll da classe pai para adicionar funcionalidades específicas
 * @param array $conditions Condições de busca (opcional)
 * @param string $orderBy Campo para ordenação (opcional)
 * @param string $direction Direção da ordenação (ASC/DESC)
 * @param int $limit Limite de registros
 * @param int $offset Deslocamento para paginação
 * @return array
 */
public function getAll(
    $conditions = [],
    $orderBy = null,
    $direction = 'ASC',
    $limit = null,
    $offset = null
) {
    // Se o orderBy não for especificado, usamos 'nome' como padrão para especialidades
    if ($orderBy === null) {
        $orderBy = 'nome';
    }
    
    // Chamamos o método da classe pai com os mesmos parâmetros
    return parent::getAll($conditions, $orderBy, $direction, $limit, $offset);
}
}
