<?php
/**
 * Controller para salvar dados do próprio perfil do usuário
 * Permite apenas: nome, email, senha e foto
 * NÃO permite alterar: perfil_id, clinica_id, parent_id, status
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=dashboard');
    exit;
}

// Garante que o usuário só pode editar seu próprio perfil
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id !== $_SESSION['usuario_id']) {
    $_SESSION['erro'] = 'Você só pode editar seu próprio perfil.';
    header('Location: index.php?module=dashboard');
    exit;
}

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Validação básica
if (empty($nome)) {
    $_SESSION['erro'] = 'O nome é obrigatório.';
    header('Location: index.php?module=usuarios&action=profile');
    exit;
}

if (empty($email)) {
    $_SESSION['erro'] = 'O e-mail é obrigatório.';
    header('Location: index.php?module=usuarios&action=profile');
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();

    // Verificar se o email já está em uso por outro usuário
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $_SESSION['erro'] = 'Este e-mail já está em uso por outro usuário.';
        header('Location: index.php?module=usuarios&action=profile');
        exit;
    }

    // Processar upload de foto
    $fotoNome = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = ROOT_PATH . '/uploads/usuarios/';

        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validar tipo de arquivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['foto']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['erro'] = 'Tipo de arquivo não permitido. Use JPG, PNG ou GIF.';
            header('Location: index.php?module=usuarios&action=profile');
            exit;
        }

        // Validar tamanho (máximo 2MB)
        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $_SESSION['erro'] = 'A foto deve ter no máximo 2MB.';
            header('Location: index.php?module=usuarios&action=profile');
            exit;
        }

        // Gerar nome único para o arquivo
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoNome = 'user_' . $id . '_' . time() . '.' . $ext;

        // Mover arquivo
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoNome)) {
            $_SESSION['erro'] = 'Erro ao fazer upload da foto.';
            header('Location: index.php?module=usuarios&action=profile');
            exit;
        }

        // Remover foto antiga se existir
        $stmt = $pdo->prepare("SELECT foto FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $fotoAntiga = $stmt->fetchColumn();
        if ($fotoAntiga && file_exists($uploadDir . $fotoAntiga)) {
            unlink($uploadDir . $fotoAntiga);
        }
    }

    // Montar query de atualização
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        if ($fotoNome) {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, foto = ? WHERE id = ?";
            $params = [$nome, $email, $senhaHash, $fotoNome, $id];
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
            $params = [$nome, $email, $senhaHash, $id];
        }
    } else {
        if ($fotoNome) {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, foto = ? WHERE id = ?";
            $params = [$nome, $email, $fotoNome, $id];
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
            $params = [$nome, $email, $id];
        }
    }

    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute($params);

    if ($resultado) {
        // Atualizar dados na sessão
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_email'] = $email;
        if ($fotoNome) {
            $_SESSION['usuario_foto'] = $fotoNome;
        }

        $_SESSION['sucesso'] = 'Perfil atualizado com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao atualizar perfil.';
    }

} catch (PDOException $e) {
    $_SESSION['erro'] = 'Erro de banco de dados: ' . $e->getMessage();
}

header('Location: index.php?module=usuarios&action=profile');
exit;
