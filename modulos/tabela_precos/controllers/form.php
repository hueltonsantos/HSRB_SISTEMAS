<?php
verificar_acesso('price_manage'); // Permission check

$model = new PrecoModel();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$preco = [
    'id' => '',
    'procedimento' => '',
    'especialidade_id' => '',
    'valor_paciente' => '0.00',
    'valor_repasse' => '0.00',
    'status' => 1
];

if ($id) {
    // Fetch by ID. Since PrecoModel->listarCompleto is complex, maybe add getById to Model, 
    // or just fetch raw for simple edit.
    $data = $model->getById($id);
    if ($data) {
        $preco = $data;
    } else {
        die('Procedimento não encontrado');
    }
}

$especialidades = $model->getEspecialidades();

require PRECOS_TEMPLATE_PATH . 'form.php';
