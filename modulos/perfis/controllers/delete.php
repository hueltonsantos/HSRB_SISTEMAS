<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    // Check if system profile
    if ($id <= 3) {
        die("Não é possível excluir perfis do sistema.");
    }
    
    $model = new PerfilModel();
    // Delete associations first? Model doesn't cascade usually unless DB FK does.
    // DB Foreign Keys are set to CASCADE for permissions, but restricted for users.
    // So if users exist, it might fail. Ideally check first.
    
    try {
        $model->delete($id);
    } catch (Exception $e) {
        // Simple error handling
        echo "<script>alert('Erro ao excluir: Perfil pode estar em uso.'); window.location.href='index.php?module=perfis';</script>";
        exit;
    }
}

header('Location: index.php?module=perfis');
exit;
