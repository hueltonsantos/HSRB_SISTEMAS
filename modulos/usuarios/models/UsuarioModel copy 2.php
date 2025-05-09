<?php
require_once __DIR__ . '/../../../Model.php';

class UsuarioModel extends Model {
    protected $table = 'usuarios';
    
    public function validarLogin($email, $senha) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND status = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Atualiza último acesso
            $this->atualizarUltimoAcesso($usuario['id']);
            return $usuario;
        }
        
        return false;
    }
    
    public function atualizarUltimoAcesso($id) {
        $sql = "UPDATE {$this->table} SET ultimo_acesso = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function listar($filtros = []) {
        $where = "WHERE 1=1";
        $params = [];
        
        if (isset($filtros['nome']) && !empty($filtros['nome'])) {
            $where .= " AND nome LIKE ?";
            $params[] = "%{$filtros['nome']}%";
        }
        
        if (isset($filtros['email']) && !empty($filtros['email'])) {
            $where .= " AND email LIKE ?";
            $params[] = "%{$filtros['email']}%";
        }
        
        if (isset($filtros['nivel_acesso']) && !empty($filtros['nivel_acesso'])) {
            $where .= " AND nivel_acesso = ?";
            $params[] = $filtros['nivel_acesso'];
        }
        
        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $where .= " AND status = ?";
            $params[] = $filtros['status'];
        }
        
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarPorId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function inserir($dados) {
        // Hash na senha
        $dados['senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        
        $campos = implode(", ", array_keys($dados));
        $placeholders = implode(", ", array_fill(0, count($dados), "?"));
        
        $sql = "INSERT INTO {$this->table} ({$campos}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute(array_values($dados));
    }
    
    public function atualizar($id, $dados) {
        // Se a senha foi enviada, faz o hash
        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT);
        } else {
            // Se a senha estiver vazia, remove do array para não atualizar
            unset($dados['senha']);
        }
        
        $sets = [];
        foreach ($dados as $campo => $valor) {
            $sets[] = "{$campo} = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(", ", $sets) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        // Adiciona o ID ao final dos valores
        $valores = array_values($dados);
        $valores[] = $id;
        
        return $stmt->execute($valores);
    }
    
    public function deletar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$id]);
    }
    
    public function alterarStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$status, $id]);
    }
}