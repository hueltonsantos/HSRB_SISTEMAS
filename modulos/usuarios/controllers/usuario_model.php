<?php
/**
 * Modelo para gerenciar usuários no banco de dados
 */
class UsuarioModel {
    private $pdo;
    
    public function __construct() {
        // Conectar ao banco de dados
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    
    /**
     * Lista todos os usuários
     */
    public function listarTodos() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM usuarios ORDER BY nome");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca um usuário pelo ID
     */
    public function buscarPorId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se um email já existe (exceto para o ID especificado)
     */
    public function emailExiste($email, $id_excecao = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id_excecao]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return true; // Em caso de erro, assumir que existe para prevenir duplicação
        }
    }
    
    /**
     * Insere um novo usuário
     */
    public function inserir($dados) {
        try {
            // Garantir que a senha esteja presente para novos usuários
            if (!isset($dados['senha']) || empty($dados['senha'])) {
                return false;
            }
            
            $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $dados['nome'],
                $dados['email'],
                $dados['senha'], // Já deve ter o hash aplicado
                $dados['nivel_acesso'],
                $dados['status']
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao inserir usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza um usuário existente
     */
    public function atualizar($id, $dados) {
        try {
            // Se a senha foi fornecida, atualizar também a senha
            if (isset($dados['senha']) && !empty($dados['senha'])) {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, nivel_acesso = ?, status = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $dados['nome'],
                    $dados['email'],
                    $dados['senha'], // Já deve ter o hash aplicado
                    $dados['nivel_acesso'],
                    $dados['status'],
                    $id
                ]);
            } else {
                // Atualizar sem alterar a senha
                $sql = "UPDATE usuarios SET nome = ?, email = ?, nivel_acesso = ?, status = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $dados['nome'],
                    $dados['email'],
                    $dados['nivel_acesso'],
                    $dados['status'],
                    $id
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exclui um usuário
     */
    public function excluir($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Autenticar usuário por email e senha
     */
    public function autenticar($email, $senha) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND status = 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Atualizar último acesso
                $this->pdo->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?")
                     ->execute([$usuario['id']]);
                return $usuario;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }
}