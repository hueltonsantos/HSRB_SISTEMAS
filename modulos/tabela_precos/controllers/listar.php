<?php
$model = new PrecoModel();

$filtros = [
    'procedimento' => isset($_GET['procedimento']) ? $_GET['procedimento'] : '',
    'especialidade_id' => isset($_GET['especialidade_id']) ? $_GET['especialidade_id'] : '',
    'clinica_id' => isset($_GET['clinica_id']) ? $_GET['clinica_id'] : ''
];

$precos = $model->listarCompleto($filtros);
$especialidades = $model->getEspecialidades();
$clinicas = $model->getClinicas();

require PRECOS_TEMPLATE_PATH . 'listar.php';
