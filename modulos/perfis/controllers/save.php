<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=perfis');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$status = isset($_POST['status']) ? 1 : 0;
$permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

if (empty($nome)) {
    die('Nome é obrigatório');
}

$model = new PerfilModel();

$data = [
    'nome' => $nome,
    'descricao' => $descricao,
    'status' => $status
];

$isNew = !$id;

if ($id) {
    // Update
    $data['id'] = $id;
    $model->save($data);
} else {
    // Insert
    $id = $model->save($data);
}

// Update Permissions
$model->updatePermissions($id, $permissions);

// Registrar log
registrarLog($isNew ? 'criar' : 'editar', 'perfis', "Perfil '$nome' " . ($isNew ? 'criado' : 'atualizado'), $id, null, [
    'nome' => $nome,
    'permissoes' => $permissions
]);

// Atualizar a sessão do usuário logado se ele pertencer ao perfil editado
if (isset($_SESSION['perfil_id']) && $_SESSION['perfil_id'] == $id) {
    // Buscar chaves das permissões atualizadas
    $db = Database::getInstance();
    $stmtPerm = $db->getConnection()->prepare("
        SELECT pm.chave 
        FROM permissoes pm
        JOIN perfil_permissoes pp ON pp.permissao_id = pm.id
        WHERE pp.perfil_id = ?
    ");
    $stmtPerm->execute([$id]);
    $_SESSION['permissoes'] = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);
    
    // Atualiza também o nome do perfil na sessão caso tenha mudado
    $_SESSION['perfil_nome'] = $nome;
}

header('Location: index.php?module=perfis');
exit;
