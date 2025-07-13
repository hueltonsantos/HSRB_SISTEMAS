<?php
/**
 * Modelo para gerenciamento de procedimentos
 */
class ProcedimentoModel {
    /**
     * Conexão com o banco de dados
     * @var PDO
     */
    private $db;
    
    /**
     * Nome da tabela no banco de dados
     * @var string
     */
    private $table = 'procedimentos';
    
    /**
     * Construtor
     */
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Busca um procedimento pelo ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca procedimentos por especialidade
     * @param int $especialidadeId
     * @return array
     */
    public function getByEspecialidade($especialidadeId) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidade_id = :especialidade_id ORDER BY nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':especialidade_id', $especialidadeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Salva um procedimento (insere ou atualiza)
     * @param array $data
     * @return int ID do procedimento
     */
    public function save($data) {
        if (isset($data['id']) && !empty($data['id'])) {
            return $this->update($data);
        } else {
            return $this->insert($data);
        }
    }
    
    /**
     * Insere um novo procedimento
     * @param array $data
     * @return int
     */
    private function insert($data) {
        $sql = "INSERT INTO {$this->table} (especialidade_id, nome, valor, status) 
                VALUES (:especialidade_id, :nome, :valor, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':especialidade_id', $data['especialidade_id'], PDO::PARAM_INT);
        $stmt->bindParam(':nome', $data['nome'], PDO::PARAM_STR);
        $stmt->bindParam(':valor', $data['valor'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        $stmt->execute();
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Atualiza um procedimento existente
     * @param array $data
     * @return int
     */
    private function update($data) {
        $sql = "UPDATE {$this->table} 
                SET especialidade_id = :especialidade_id,
                    nome = :nome,
                    valor = :valor,
                    status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmt->bindParam(':especialidade_id', $data['especialidade_id'], PDO::PARAM_INT);
        $stmt->bindParam(':nome', $data['nome'], PDO::PARAM_STR);
        $stmt->bindParam(':valor', $data['valor'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        $stmt->execute();
        
        return $data['id'];
    }
    
    /**
     * Exclui um procedimento
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}