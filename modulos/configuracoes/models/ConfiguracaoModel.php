<?php
require_once __DIR__ . '/../../../Model.php';

class ConfiguracaoModel extends Model {
    protected $table = 'configuracoes';
    
    public function __construct() {
        parent::__construct();
        // Verifica se a tabela existe e, se não, cria
        $this->criarTabelaSeNaoExistir();
    }
    
    private function criarTabelaSeNaoExistir() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chave VARCHAR(50) NOT NULL UNIQUE,
            valor TEXT,
            tipo VARCHAR(20) NOT NULL DEFAULT 'texto',
            descricao TEXT,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->pdo->exec($sql);
        
        // Insere configurações padrão se a tabela estiver vazia
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $configPadrao = [
                ['nome_clinica', 'Clínica de Encaminhamento', 'texto', 'Nome da clínica'],
                ['endereco_clinica', 'Endereço da Clínica', 'texto', 'Endereço completo da clínica'],
                ['telefone_clinica', '(00) 0000-0000', 'texto', 'Telefone principal da clínica'],
                ['email_clinica', 'contato@clinica.com', 'texto', 'E-mail de contato da clínica'],
                ['logo', '', 'arquivo', 'Logo da clínica'],
                ['qtd_itens_paginacao', '10', 'numero', 'Quantidade de itens por página nas listagens'],
                ['permitir_agendamento_simultaneo', '0', 'booleano', 'Permitir agendamento de consultas em horários simultâneos'],
                ['intervalo_consultas', '30', 'numero', 'Intervalo entre consultas em minutos'],
                ['hora_inicio_atendimento', '08:00', 'hora', 'Hora de início do atendimento'],
                ['hora_fim_atendimento', '18:00', 'hora', 'Hora de término do atendimento'],
                ['dias_atendimento', '1,2,3,4,5', 'lista', 'Dias da semana com atendimento (1=Segunda a 7=Domingo)']
            ];
            
            $sql = "INSERT INTO {$this->table} (chave, valor, tipo, descricao) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($configPadrao as $config) {
                $stmt->execute($config);
            }
        }
    }
    
    public function listar() {
        $sql = "SELECT * FROM {$this->table} ORDER BY chave ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarPorChave($chave) {
        $sql = "SELECT * FROM {$this->table} WHERE chave = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$chave]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obterValor($chave, $padrao = '') {
        $configuracao = $this->buscarPorChave($chave);
        
        if ($configuracao) {
            return $configuracao['valor'];
        }
        
        return $padrao;
    }
    
    public function atualizarConfiguracao($chave, $valor) {
        $sql = "UPDATE {$this->table} SET valor = ? WHERE chave = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$valor, $chave]);
    }
    
    public function atualizarConfiguracoes($dados) {
        $sucesso = true;
        
        foreach ($dados as $chave => $valor) {
            $sucesso = $sucesso && $this->atualizarConfiguracao($chave, $valor);
        }
        
        return $sucesso;
    }
    
    public function adicionarConfiguracao($chave, $valor, $tipo = 'texto', $descricao = '') {
        // Verifica se já existe
        $configuracao = $this->buscarPorChave($chave);
        
        if ($configuracao) {
            return $this->atualizarConfiguracao($chave, $valor);
        }
        
        // Caso não exista, insere nova
        $sql = "INSERT INTO {$this->table} (chave, valor, tipo, descricao) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$chave, $valor, $tipo, $descricao]);
    }
    
    public function removerConfiguracao($chave) {
        $sql = "DELETE FROM {$this->table} WHERE chave = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$chave]);
    }
}