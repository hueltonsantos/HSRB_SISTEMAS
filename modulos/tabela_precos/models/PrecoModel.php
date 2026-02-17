<?php
class PrecoModel extends Model {
    protected $table = 'valores_procedimentos';

    public function listarCompleto($filtros = []) {
        $sql = "SELECT 
                    vp.*,
                    e.nome AS especialidade,
                    cp.nome AS clinica,
                    cp.endereco, cp.numero, cp.bairro, cp.cidade, cp.estado
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
        
        if (!empty($filtros['procedimento'])) {
            $sql .= " AND vp.procedimento LIKE ?";
            $params[] = "%{$filtros['procedimento']}%";
        }
        
        if (!empty($filtros['especialidade_id'])) {
            $sql .= " AND e.id = ?";
            $params[] = $filtros['especialidade_id'];
        }
        
        if (!empty($filtros['clinica_id'])) {
            $sql .= " AND cp.id = ?";
            $params[] = $filtros['clinica_id'];
        }
        
        $sql .= " ORDER BY vp.procedimento, cp.nome";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getEspecialidades() {
        return $this->db->fetchAll("SELECT * FROM especialidades WHERE status = 1 ORDER BY nome");
    }

    public function getClinicas() {
        return $this->db->fetchAll("SELECT * FROM clinicas_parceiras WHERE status = 1 ORDER BY nome");
    }
}
