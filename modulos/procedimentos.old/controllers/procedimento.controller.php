<?php
class ProcedimentoController {
    private $model;
    
    public function __construct($database) {
        $this->model = new ProcedimentoModel($database);
    }
    
    public function listar() {
        return $this->model->listarProcedimentos();
    }
    
    public function buscarPorId($id) {
        return $this->model->buscarPorId($id);
    }
    
    public function salvar($dados) {
        // Validação básica
        if (empty($dados['procedimento'])) {
            return ['erro' => 'O nome do procedimento é obrigatório'];
        }
        
        if (empty($dados['especialidade_id'])) {
            return ['erro' => 'A especialidade é obrigatória'];
        }
        
        if (!is_numeric($dados['valor'])) {
            return ['erro' => 'O valor deve ser numérico'];
        }
        
        // Formatar o valor para formato de banco de dados
        $dados['valor'] = str_replace(',', '.', $dados['valor']);
        
        $resultado = $this->model->salvar($dados);
        if ($resultado) {
            return ['sucesso' => 'Procedimento salvo com sucesso'];
        } else {
            return ['erro' => 'Erro ao salvar o procedimento'];
        }
    }
    
    public function excluir($id) {
        $resultado = $this->model->excluir($id);
        if ($resultado) {
            return ['sucesso' => 'Procedimento excluído com sucesso'];
        } else {
            return ['erro' => 'Erro ao excluir o procedimento'];
        }
    }
}
?>