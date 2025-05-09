<?php
class PrecosController {
    private $model;
    
    public function __construct($database) {
        $this->model = new PrecosModel($database);
    }
    
    public function listarTudo() {
        return $this->model->listarTudo();
    }
    
    public function filtrarProcedimentos($filtros) {
        return $this->model->filtrarProcedimentos($filtros);
    }
    
    public function listarEspecialidades() {
        return $this->model->listarEspecialidades();
    }
    
    public function listarClinicas() {
        return $this->model->listarClinicas();
    }
}
?>