<?php
/**
 * Modelo para gerenciamento do relacionamento entre clínicas e especialidades
 */
class ClinicaEspecialidadeModel {
    /**
     * Conexão com o banco de dados
     * @var PDO
     */
    private $db;
    
    /**
     * Nome da tabela no banco de dados
     * @var string
     */
    private $table = 'clinicas_especialidades';
    
    /**
     * Construtor
     */
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Verifica se uma especialidade já está vinculada a uma clínica
     * @param int $clinicaId
     * @param int $especialidadeId
     * @return bool
     */
    public function checkVinculo($clinicaId, $especialidadeId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE clinica_id = :clinica_id AND especialidade_id = :especialidade_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':clinica_id', $clinicaId, PDO::PARAM_INT);
        $stmt->bindParam(':especialidade_id', $especialidadeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Adiciona vínculo entre clínica e especialidade
     * @param int $clinicaId
     * @param int $especialidadeId
     * @return bool
     */
    public function addVinculo($clinicaId, $especialidadeId) {
        $sql = "INSERT INTO {$this->table} (clinica_id, especialidade_id) 
                VALUES (:clinica_id, :especialidade_id)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':clinica_id', $clinicaId, PDO::PARAM_INT);
        $stmt->bindParam(':especialidade_id', $especialidadeId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Remove vínculo entre clínica e especialidade
     * @param int $clinicaId
     * @param int $especialidadeId
     * @return bool
     */
    public function removeVinculo($clinicaId, $especialidadeId) {
        $sql = "DELETE FROM {$this->table} 
                WHERE clinica_id = :clinica_id AND especialidade_id = :especialidade_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':clinica_id', $clinicaId, PDO::PARAM_INT);
        $stmt->bindParam(':especialidade_id', $especialidadeId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Busca todas as especialidades de uma clínica
     * @param int $clinicaId
     * @return array
     */
    public function getByClinica($clinicaId) {
        $sql = "SELECT e.* 
                FROM especialidades e
                JOIN {$this->table} ce ON e.id = ce.especialidade_id
                WHERE ce.clinica_id = :clinica_id AND e.status = 'Ativa'
                ORDER BY e.nome ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':clinica_id', $clinicaId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca todas as clínicas de uma especialidade
     * @param int $especialidadeId
     * @return array
     */
    public function getByEspecialidade($especialidadeId) {
        $sql = "SELECT c.* 
                FROM clinicas c
                JOIN {$this->table} ce ON c.id = ce.clinica_id
                WHERE ce.especialidade_id = :especialidade_id AND c.status = 'Ativa'
                ORDER BY c.nome ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':especialidade_id', $especialidadeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}