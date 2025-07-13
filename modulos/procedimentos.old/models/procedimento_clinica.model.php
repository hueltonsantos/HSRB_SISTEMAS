<?php
class ProcedimentoClinicaModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function listarProcedimentosClinicas() {
        $query = "SELECT pc.*, 
                 vp.procedimento, 
                 cp.nome as clinica_nome, 
                 cp.endereco, cp.numero, cp.bairro, cp.cidade, cp.estado
                 FROM procedimentos_clinicas pc
                 JOIN valores_procedimentos vp ON pc.procedimento_id = vp.id
                 JOIN clinicas_parceiras cp ON pc.clinica_id = cp.id
                 ORDER BY vp.procedimento, cp.nome";
        return $this->db->query($query);
    }
    
    public function buscarPorId($id) {
        $query = "SELECT * FROM procedimentos_clinicas WHERE id = ?";
        return $this->db->query($query, [$id])->fetch_assoc();
    }
    
    public function buscarPorProcedimento($procedimento_id) {
        $query = "SELECT pc.*, cp.nome as clinica_nome, 
                 cp.endereco, cp.numero, cp.bairro, cp.cidade, cp.estado
                 FROM procedimentos_clinicas pc
                 JOIN clinicas_parceiras cp ON pc.clinica_id = cp.id
                 WHERE pc.procedimento_id = ?";
        return $this->db->query($query, [$procedimento_id]);
    }
    
    public function salvar($dados) {
        // Verifica se já existe um registro para este procedimento nesta clínica
        $verificar = "SELECT id FROM procedimentos_clinicas 
                      WHERE procedimento_id = ? AND clinica_id = ?";
        $existente = $this->db->query($verificar, [
            $dados['procedimento_id'], 
            $dados['clinica_id']
        ])->fetch_assoc();
        
        if ($existente) {
            $dados['id'] = $existente['id'];
            return $this->atualizar($dados);
        } else {
            return $this->inserir($dados);
        }
    }
    
    private function inserir($dados) {
        $query = "INSERT INTO procedimentos_clinicas 
                  (procedimento_id, clinica_id, valor, observacoes, status) 
                  VALUES (?, ?, ?, ?, ?)";
        return $this->db->query($query, [
            $dados['procedimento_id'],
            $dados['clinica_id'],
            $dados['valor'],
            $dados['observacoes'] ?? '',
            $dados['status'] ?? 1
        ]);
    }
    
    private function atualizar($dados) {
        $query = "UPDATE procedimentos_clinicas 
                  SET valor = ?, observacoes = ?, status = ? 
                  WHERE id = ?";
        return $this->db->query($query, [
            $dados['valor'],
            $dados['observacoes'] ?? '',
            $dados['status'] ?? 1,
            $dados['id']
        ]);
    }
    
    public function excluir($id) {
        $query = "DELETE FROM procedimentos_clinicas WHERE id = ?";
        return $this->db->query($query, [$id]);
    }
}
?>