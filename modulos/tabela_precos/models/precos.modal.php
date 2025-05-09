<?php
class PrecosModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function listarTudo() {
        $query = "SELECT 
                    vp.id AS procedimento_id,
                    vp.procedimento,
                    vp.valor AS valor_procedimento,
                    e.id AS especialidade_id,
                    e.nome AS especialidade,
                    cp.id AS clinica_id,
                    cp.nome AS clinica,
                    cp.endereco,
                    cp.numero,
                    cp.bairro,
                    cp.cidade,
                    cp.estado
                FROM 
                    valores_procedimentos vp
                LEFT JOIN 
                    especialidades e ON vp.especialidade_id = e.id
                LEFT JOIN 
                    especialidades_clinicas ec ON e.id = ec.especialidade_id
                LEFT JOIN 
                    clinicas_parceiras cp ON ec.clinica_id = cp.id
                WHERE
                    vp.status = 1 
                    AND (cp.id IS NULL OR cp.status = 1)
                ORDER BY 
                    vp.procedimento, cp.nome";
        
        return $this->db->query($query);
    }
    
    public function filtrarProcedimentos($filtros) {
        $query = "SELECT 
                    vp.id AS procedimento_id,
                    vp.procedimento,
                    vp.valor AS valor_procedimento,
                    e.id AS especialidade_id,
                    e.nome AS especialidade,
                    cp.id AS clinica_id,
                    cp.nome AS clinica,
                    cp.endereco,
                    cp.numero,
                    cp.bairro,
                    cp.cidade,
                    cp.estado
                FROM 
                    valores_procedimentos vp
                LEFT JOIN 
                    especialidades e ON vp.especialidade_id = e.id
                LEFT JOIN 
                    especialidades_clinicas ec ON e.id = ec.especialidade_id
                LEFT JOIN 
                    clinicas_parceiras cp ON ec.clinica_id = cp.id
                WHERE
                    vp.status = 1 
                    AND (cp.id IS NULL OR cp.status = 1)";
        
        $params = [];
        
        // Filtro por procedimento
        if (!empty($filtros['procedimento'])) {
            $query .= " AND vp.procedimento LIKE ?";
            $params[] = "%" . $filtros['procedimento'] . "%";
        }
        
        // Filtro por especialidade
        if (!empty($filtros['especialidade_id'])) {
            $query .= " AND e.id = ?";
            $params[] = $filtros['especialidade_id'];
        }
        
        // Filtro por clínica
        if (!empty($filtros['clinica_id'])) {
            $query .= " AND cp.id = ?";
            $params[] = $filtros['clinica_id'];
        }
        
        $query .= " ORDER BY vp.procedimento, cp.nome";
        
        return $this->db->query($query, $params);
    }
    
    public function listarEspecialidades() {
        $query = "SELECT * FROM especialidades WHERE status = 1 ORDER BY nome";
        return $this->db->query($query);
    }
    
    public function listarClinicas() {
        $query = "SELECT * FROM clinicas_parceiras WHERE status = 1 ORDER BY nome";
        return $this->db->query($query);
    }
}
?>