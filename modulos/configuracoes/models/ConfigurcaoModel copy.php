<?php
require_once __DIR__ . '/../../../Model.php';

class ConfiguracaoModel extends Model {
    protected $table = 'configuracoes';
    protected $primaryKey = 'id';
    
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
        
        $this->db->query($sql);
        
        // Insere configurações padrão se a tabela estiver vazia
        $count = $this->count();
        
        if ($count == 0) {
            $configPadrao = [
                ['chave' => 'nome_clinica', 'valor' => 'Clínica de Encaminhamento', 'tipo' => 'texto', 'descricao' => 'Nome da clínica'],
                ['chave' => 'endereco_clinica', 'valor' => 'Endereço da Clínica', 'tipo' => 'texto', 'descricao' => 'Endereço completo da clínica'],
                ['chave' => 'telefone_clinica', 'valor' => '(00) 0000-0000', 'tipo' => 'texto', 'descricao' => 'Telefone principal da clínica'],
                ['chave' => 'email_clinica', 'valor' => 'contato@clinica.com', 'tipo' => 'texto', 'descricao' => 'E-mail de contato da clínica'],
                ['chave' => 'logo', 'valor' => '', 'tipo' => 'arquivo', 'descricao' => 'Logo da clínica'],
                ['chave' => 'qtd_itens_paginacao', 'valor' => '10', 'tipo' => 'numero', 'descricao' => 'Quantidade de itens por página nas listagens'],
                ['chave' => 'permitir_agendamento_simultaneo', 'valor' => '0', 'tipo' => 'booleano', 'descricao' => 'Permitir agendamento de consultas em horários simultâneos'],
                ['chave' => 'intervalo_consultas', 'valor' => '30', 'tipo' => 'numero', 'descricao' => 'Intervalo entre consultas em minutos'],
                ['chave' => 'hora_inicio_atendimento', 'valor' => '08:00', 'tipo' => 'hora', 'descricao' => 'Hora de início do atendimento'],
                ['chave' => 'hora_fim_atendimento', 'valor' => '18:00', 'tipo' => 'hora', 'descricao' => 'Hora de término do atendimento'],
                ['chave' => 'dias_atendimento', 'valor' => '1,2,3,4,5', 'tipo' => 'lista', 'descricao' => 'Dias da semana com atendimento (1=Segunda a 7=Domingo)']
            ];
            
            foreach ($configPadrao as $config) {
                $this->insert($config);
            }
        }
    }
    
    public function listar() {
        return $this->getAll([], 'chave', 'ASC');
    }
    
    public function buscarPorChave($chave) {
        return $this->getOneWhere(['chave' => $chave]);
    }
    
    public function obterValor($chave, $padrao = '') {
        $configuracao = $this->buscarPorChave($chave);
        
        if ($configuracao) {
            return $configuracao['valor'];
        }
        
        return $padrao;
    }
    
    public function atualizarConfiguracao($chave, $valor) {
        $configuracao = $this->buscarPorChave($chave);
        
        if ($configuracao) {
            return $this->update($configuracao['id'], ['valor' => $valor]);
        }
        
        return false;
    }
    
    public function atualizarConfiguracoes($dados) {
        $sucesso = true;
        
        foreach ($dados as $chave => $valor) {
            $resultado = $this->atualizarConfiguracao($chave, $valor);
            $sucesso = $sucesso && $resultado;
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
        return $this->insert([
            'chave' => $chave,
            'valor' => $valor,
            'tipo' => $tipo,
            'descricao' => $descricao
        ]);
    }
    
    public function removerConfiguracao($chave) {
        $configuracao = $this->buscarPorChave($chave);
        
        if ($configuracao) {
            return $this->delete($configuracao['id']);
        }
        
        return false;
    }
    
    public function listarPorTipo() {
        $configuracoes = $this->listar();
        $configPorTipo = [];
        
        foreach ($configuracoes as $config) {
            if (!isset($configPorTipo[$config['tipo']])) {
                $configPorTipo[$config['tipo']] = [];
            }
            
            $configPorTipo[$config['tipo']][] = $config;
        }
        
        return $configPorTipo;
    }
}