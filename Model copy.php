<?php
/**
 * Classe Model - Classe base para todos os modelos
 * Implementa operações básicas de CRUD
 */
abstract class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected $db;
    
    /**
     * Construtor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Busca todos os registros
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
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        // Adiciona condições WHERE
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // Adiciona ordenação
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        
        // Adiciona limite e offset para paginação
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Busca um registro pelo ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Busca registros com base em condições específicas
     * @param array $conditions
     * @return array
     */
    public function getWhere($conditions) {
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $whereClause[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $whereClause);
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Busca um único registro com base em condições específicas
     * @param array $conditions
     * @return array|null
     */
    public function getOneWhere($conditions) {
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $whereClause[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $whereClause) . " LIMIT 1";
        return $this->db->fetchOne($sql, $params);
    }
    
    /**
     * Salva um registro (insere ou atualiza)
     * @param array $data
     * @return int
     */
    public function save($data) {
        if (isset($data[$this->primaryKey]) && $data[$this->primaryKey]) {
            $id = $data[$this->primaryKey];
            unset($data[$this->primaryKey]);
            return $this->update($id, $data);
        } else {
            if (isset($data[$this->primaryKey])) {
                unset($data[$this->primaryKey]);
            }
            return $this->insert($data);
        }
    }
    
    /**
     * Insere um novo registro
     * @param array $data
     * @return int
     */
    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Atualiza um registro existente
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data) {
        return $this->db->update(
            $this->table,
            $data,
            "{$this->primaryKey} = ?",
            [$id]
        );
    }
    
    /**
     * Exclui um registro pelo ID
     * @param int $id
     * @return int
     */
    public function delete($id) {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$id]
        );
    }
    
    /**
     * Desativa um registro (exclusão lógica)
     * @param int $id
     * @return int
     */
    public function deactivate($id) {
        return $this->db->update(
            $this->table,
            ['status' => 0],
            "{$this->primaryKey} = ?",
            [$id]
        );
    }
    
    /**
     * Conta total de registros com base em condições
     * @param array $conditions
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return (int) $result['total'];
    }
    
    /**
     * Verifica se um registro existe
     * @param array $conditions
     * @return bool
     */
    public function exists($conditions) {
        return $this->count($conditions) > 0;
    }
}