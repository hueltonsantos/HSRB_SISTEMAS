<?php
$model = new PerfilModel();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$perfil = [
    'id' => '',
    'nome' => '',
    'descricao' => '',
    'status' => 1
];
$selectedPermissions = [];

if ($id) {
    $perfil = $model->getById($id);
    if (!$perfil) {
        die('Perfil não encontrado');
    }
    // Fetch permissions
    $perms = $model->getPermissions($id);
    $selectedPermissions = array_column($perms, 'permissao_id');
}

// Get all available permissions
$allPermissions = $model->getAllPermissions();

require PERFIS_TEMPLATE_PATH . 'form.php';
