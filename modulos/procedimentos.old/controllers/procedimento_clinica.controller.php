<?php
class ProcedimentoClinicaController {
    private $model;
    
    public function __construct($database) {
        $this->model = new ProcedimentoClinicaModel($database);
    }
    
    public function listar() {
        return $this->model->listarProcedimentosClinicas();
    }
    
    public function buscarPorProcedimento($procedimento_id) {
        return $this->model->buscarPorProcedimento($procedimento_id);
    }
    
    public function buscarPorId($id) {
        return $this->model->buscarPorId($id);
    }
    
    public function salvar($dados) {
        // Validação básica
        if (empty($dados['procedimento_id'])) {
            return ['erro' => 'O procedimento é obrigatório'];
        }
        
        if (empty($dados['clinica_id'])) {
            return ['erro' => 'A clínica é obrigatória'];
        }
        
        if (!is_numeric($dados['valor'])) {
            return ['erro' => 'O valor deve ser numérico'];
        }
        
        // Formatar o valor para formato de banco de dados
        $dados['valor'] = str_replace(',', '.', $dados['valor']);
        
        $resultado = $this->model->salvar($dados);
        if ($resultado) {
            return ['sucesso' => 'Vínculo de procedimento com clínica salvo com sucesso'];
        } else {
            return ['erro' => 'Erro ao salvar o vínculo'];
        }
    }
    
    public function excluir($id) {
        $resultado = $this->model->excluir($id);
        if ($resultado) {
            return ['sucesso' => 'Vínculo excluído com sucesso'];
        } else {
            return ['erro' => 'Erro ao excluir o vínculo'];
        }
    }
}
?>