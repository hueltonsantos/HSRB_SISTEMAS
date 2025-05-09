<?php
require_once __DIR__ . '/../../../Model.php';

class UsuarioModel extends Model {
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    
    public function validarLogin($email, $senha) {
        $usuario = $this->getOneWhere(['email' => $email, 'status' => 1]);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Atualiza último acesso
            $this->update($usuario['id'], ['ultimo_acesso' => date('Y-m-d H:i:s')]);
            return $usuario;
        }
        
        return false;
    }
    
    public function atualizarUltimoAcesso($id) {
        return $this->update($id, ['ultimo_acesso' => date('Y-m-d H:i:s')]);
    }
    
    public function listar($filtros = []) {
        // Se temos filtros que usam LIKE, temos que construir uma consulta customizada
        if ((isset($filtros['nome']) && !empty($filtros['nome'])) || 
            (isset($filtros['email']) && !empty($filtros['email']))) {
            
            $sql = "SELECT * FROM {$this->table} WHERE 1=1";
            $params = [];
            
            if (isset($filtros['nome']) && !empty($filtros['nome'])) {
                $sql .= " AND nome LIKE ?";
                $params[] = "%{$filtros['nome']}%";
            }
            
            if (isset($filtros['email']) && !empty($filtros['email'])) {
                $sql .= " AND email LIKE ?";
                $params[] = "%{$filtros['email']}%";
            }
            
            if (isset($filtros['nivel_acesso']) && !empty($filtros['nivel_acesso'])) {
                $sql .= " AND nivel_acesso = ?";
                $params[] = $filtros['nivel_acesso'];
            }
            
            if (isset($filtros['status']) && $filtros['status'] !== '') {
                $sql .= " AND status = ?";
                $params[] = $filtros['status'];
            }
            
            $sql .= " ORDER BY nome ASC";
            
            return $this->db->fetchAll($sql, $params);
        }
        
        // Apenas filtros exatos
        $conditions = [];
        
        if (isset($filtros['nivel_acesso']) && !empty($filtros['nivel_acesso'])) {
            $conditions['nivel_acesso'] = $filtros['nivel_acesso'];
        }
        
        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $conditions['status'] = $filtros['status'];
        }
        
        return $this->getAll($conditions, 'nome', 'ASC');
    }
    
    public function buscarPorId($id) {
        return $this->getById($id);
    }
    
    public function inserir($dados) {
        // Hash na senha
        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        }
        
        return $this->insert($dados);
    }
    
    public function atualizar($id, $dados) {
        // Se a senha foi enviada, faz o hash
        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        } else {
            // Se a senha estiver vazia, remove do array para não atualizar
            unset($dados['senha']);
        }
        
        return $this->update($id, $dados);
    }
    
    public function deletar($id) {
        return $this->delete($id);
    }
    
    public function alterarStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }
}