<?php
/**
 * Classe Database - Gerencia a conexão com o banco de dados
 * Implementa o padrão Singleton para manter apenas uma conexão
 */
class Database {
    private static $instance;
    private $connection;
    
    /**
     * Construtor privado - impede instanciação direta
     */
    private function __construct() {
        try {
            $this->connection = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Erro de conexão com o banco de dados: ' . $e->getMessage());
        }
    }
    
    /**
     * Método para obter a instância única
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtém a conexão PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Executa uma consulta e retorna o statement
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Executa uma consulta e retorna uma única linha
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Executa uma consulta e retorna todas as linhas
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insere dados e retorna o ID inserido
     * @param string $table
     * @param array $data
     * @return int
     */
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }
    
    /**
     * Atualiza dados
     * @param string $table
     * @param array $data
     * @param string $where
     * @param array $whereParams
     * @return int
     */
    public function update($table, $data, $where, $whereParams = []) {
        $sets = [];
        foreach ($data as $field => $value) {
            $sets[] = "{$field} = ?";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$where}";
        
        $params = array_merge(array_values($data), $whereParams);
        $stmt = $this->query($sql, $params);
        
        return $stmt->rowCount();
    }
    
    /**
     * Deleta registros
     * @param string $table
     * @param string $where
     * @param array $params
     * @return int
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     */
    public function commit() {
        $this->connection->commit();
    }
    
    /**
     * Cancela uma transação
     */
    public function rollBack() {
        $this->connection->rollBack();
    }
}