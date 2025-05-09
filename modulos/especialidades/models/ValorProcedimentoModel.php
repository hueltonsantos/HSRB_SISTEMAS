<?php
/**
 * Classe ValorProcedimentoModel - Gerencia operações relacionadas aos valores dos procedimentos
 */
class ValorProcedimentoModel extends Model {
    
    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'valores_procedimentos';
    }
    
    /**
     * Valida os dados do procedimento
     * @param array $data
     * @return array [success, message, errors]
     */
    public function validate($data) {
        $errors = [];
        
        // Validações básicas
        if (empty($data['especialidade_id'])) {
            $errors['especialidade_id'] = 'A especialidade é obrigatória';
        }
        
        if (empty($data['procedimento'])) {
            $errors['procedimento'] = 'O nome do procedimento é obrigatório';
        }
        
        if (!isset($data['valor']) || $data['valor'] === '') {
            $errors['valor'] = 'O valor é obrigatório';
        } else if (!is_numeric(str_replace(['R$', '.', ','], ['', '', '.'], $data['valor']))) {
            $errors['valor'] = 'O valor deve ser um número válido';
        }
        
        $success = empty($errors);
        $message = $success ? 'Dados válidos' : 'Existem erros nos dados fornecidos';
        
        return [
            'success' => $success,
            'message' => $message,
            'errors' => $errors
        ];
    }
    
    /**
     * Formata os dados antes de salvar
     * @param array $data
     * @return array
     */
    public function formatData($data) {
        // Formata o valor para o formato decimal
        if (isset($data['valor'])) {
            $data['valor'] = $this->formatCurrencyToDecimal($data['valor']);
        }
        
        return $data;
    }
    
    /**
     * Salva os dados do procedimento após validação e formatação
     * @param array $data
     * @return array [success, message, id]
     */
    public function saveValorProcedimento($data) {
        // Validar dados
        $validation = $this->validate($data);
        if (!$validation['success']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'],
                'id' => null
            ];
        }
        
        // Formatar dados
        $data = $this->formatData($data);
        
        // Salvar no banco
        try {
            $id = $this->save($data);
            return [
                'success' => true,
                'message' => 'Procedimento salvo com sucesso',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao salvar procedimento: ' . $e->getMessage(),
                'errors' => ['database' => $e->getMessage()],
                'id' => null
            ];
        }
    }
    
    /**
     * Salva múltiplos procedimentos para uma especialidade
     * @param int $especialidadeId
     * @param array $procedimentos
     * @param array $valores
     * @return array [success, message, count]
     */
    public function saveBatchProcedimentos($especialidadeId, $procedimentos, $valores) {
        $sucessos = 0;
        $falhas = 0;
        
        $this->db->beginTransaction();
        
        try {
            for ($i = 0; $i < count($procedimentos); $i++) {
                if (empty($procedimentos[$i])) continue;
                
                $data = [
                    'especialidade_id' => $especialidadeId,
                    'procedimento' => $procedimentos[$i],
                    'valor' => $valores[$i],
                    'status' => 1
                ];
                
                $resultado = $this->saveValorProcedimento($data);
                
                if ($resultado['success']) {
                    $sucessos++;
                } else {
                    $falhas++;
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => "Procedimentos salvos com sucesso. Adicionados: {$sucessos}. Falhas: {$falhas}.",
                'count' => $sucessos
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            
            return [
                'success' => false,
                'message' => 'Erro ao salvar procedimentos: ' . $e->getMessage(),
                'count' => 0
            ];
        }
    }
    
    /**
     * Converte valores monetários (R$ 1.234,56) para decimal (1234.56)
     * @param string $value
     * @return float
     */
    private function formatCurrencyToDecimal($value) {
        // Remove o símbolo de moeda e espaços
        $value = trim(str_replace('R$', '', $value));
        
        // Substitui pontos por nada (remove separador de milhares)
        $value = str_replace('.', '', $value);
        
        // Substitui vírgula por ponto (decimal)
        $value = str_replace(',', '.', $value);
        
        return (float) $value;
    }
    
    /**
     * Formata um valor decimal para exibição no formato monetário
     * @param float $value
     * @return string
     */
    public function formatDecimalToCurrency($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}