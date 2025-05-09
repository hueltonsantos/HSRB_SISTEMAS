<?php
class ProcedimentoModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function listarProcedimentos() {
        $query = "SELECT vp.*, e.nome as especialidade 
                  FROM valores_procedimentos vp
                  LEFT JOIN especialidades e ON vp.especialidade_id = e.id
                  ORDER BY procedimento";
        return $this->db->query($query);
    }
    
    public function buscarPorId($id) {
        $query = "SELECT * FROM valores_procedimentos WHERE id = ?";
        return $this->db->query($query, [$id])->fetch_assoc();
    }
    
    public function salvar($dados) {
        if (isset($dados['id']) && !empty($dados['id'])) {
            return $this->atualizar($dados);
        } else {
            return $this->inserir($dados);
        }
    }
    
    private function inserir($dados) {
        $query = "INSERT INTO valores_procedimentos 
                  (especialidade_id, procedimento, valor, status) 
                  VALUES (?, ?, ?, ?)";
        return $this->db->query($query, [
            $dados['especialidade_id'],
            $dados['procedimento'],
            $dados['valor'],
            $dados['status'] ?? 1
        ]);
    }
    
    private function atualizar($dados) {
        $query = "UPDATE valores_procedimentos 
                  SET especialidade_id = ?, procedimento = ?, valor = ?, status = ? 
                  WHERE id = ?";
        return $this->db->query($query, [
            $dados['especialidade_id'],
            $dados['procedimento'],
            $dados['valor'],
            $dados['status'] ?? 1,
            $dados['id']
        ]);
    }
    
    public function excluir($id) {
        $query = "DELETE FROM valores_procedimentos WHERE id = ?";
        return $this->db->query($query, [$id]);
    }
}
?>