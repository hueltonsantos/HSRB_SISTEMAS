<?php
/**
 * Arquivo de autenticação
 * Inclua este arquivo em todas as páginas que requerem autenticação
 */
require_once 'config.php';

// Carregar Database se não estiver carregado
if (!class_exists('Database')) {
    require_once __DIR__ . '/Database.php';
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

/**
 * Sincroniza dados do usuário e permissões do banco em tempo real
 * Isso garante que alterações feitas por admin sejam aplicadas instantaneamente
 */
function sincronizarSessao() {
    static $sincronizado = false;

    // Só sincroniza uma vez por requisição
    if ($sincronizado) {
        return;
    }
    $sincronizado = true;

    try {
        // Verificar se Database está disponível
        if (!class_exists('Database')) {
            return;
        }

        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Buscar dados atualizados do usuário
        $stmt = $pdo->prepare("
            SELECT u.*, p.nome as perfil_nome
            FROM usuarios u
            LEFT JOIN perfis p ON u.perfil_id = p.id
            WHERE u.id = ? AND u.status = 1
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se usuário não existe mais ou foi desativado, fazer logout
        if (!$usuario) {
            session_destroy();
            header('Location: login.php?msg=sessao_expirada');
            exit;
        }

        // Atualizar dados na sessão
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['perfil_id'] = $usuario['perfil_id'];
        $_SESSION['perfil_nome'] = $usuario['perfil_nome'];
        $_SESSION['clinica_id'] = $usuario['clinica_id'];
        $_SESSION['usuario_foto'] = isset($usuario['foto']) ? $usuario['foto'] : null;

        // Buscar permissões atualizadas do perfil
        $stmtPerm = $pdo->prepare("
            SELECT pm.chave
            FROM permissoes pm
            JOIN perfil_permissoes pp ON pp.permissao_id = pm.id
            WHERE pp.perfil_id = ?
        ");
        $stmtPerm->execute([$usuario['perfil_id']]);
        $_SESSION['permissoes'] = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);

    } catch (Exception $e) {
        // Em caso de erro, mantém sessão atual
        error_log("Erro ao sincronizar sessão: " . $e->getMessage());
    }
}

// Sincronizar sessão automaticamente
sincronizarSessao();

/**
 * Verifica se o usuário tem uma permissão específica
 * @param string $permissionKey Chave da permissão (ex: 'user_manage')
 * @return bool
 */
function hasPermission($permissionKey) {
    if (!isset($_SESSION['permissoes']) || !is_array($_SESSION['permissoes'])) {
        return false;
    }

    // Super Admin: hueltonti@gmail.com sempre tem acesso total
    if (isset($_SESSION['usuario_email']) && $_SESSION['usuario_email'] == 'hueltonti@gmail.com') {
        return true;
    }

    // Verifica se a chave corresponde a uma das permissões do usuário
    if (in_array($permissionKey, $_SESSION['permissoes'])) {
        return true;
    }

    // Suporte legado: verifica se a chave corresponde ao nome do perfil do usuário
    if (isset($_SESSION['perfil_nome']) && strtolower($_SESSION['perfil_nome']) == strtolower($permissionKey)) {
        return true;
    }

    return false;
}

/**
 * Função para verificar se o usuário tem permissão para acessar determinada página
 * @param string|array $requiredPermission Chave da permissão ou array de chaves
 * @param string $redirect_url URL para redirecionar em caso de acesso negado
 * @return bool
 */
function verificar_acesso($requiredPermission, $redirect_url = 'acesso_negado.php') {
    $allowed = false;

    if (is_array($requiredPermission)) {
        foreach ($requiredPermission as $perm) {
            if (hasPermission($perm)) {
                $allowed = true;
                break;
            }
        }
    } else {
        $allowed = hasPermission($requiredPermission);
    }

    if (!$allowed) {
        // Se for AJAX, retornar JSON error
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('HTTP/1.1 403 Forbidden');
            exit(json_encode(['error' => 'Acesso negado']));
        }

        if ($redirect_url) {
            header('Location: ' . $redirect_url);
            exit;
        } else {
            die("Acesso negado.");
        }
    }
    return true;
}

/**
 * Registra uma atividade no log do sistema
 * @param string $acao Tipo de ação (criar, editar, excluir, login, etc)
 * @param string $modulo Módulo onde ocorreu a ação
 * @param string $descricao Descrição detalhada da ação
 * @param int|null $registro_id ID do registro afetado (opcional)
 * @param array|null $dados_anteriores Dados antes da alteração (opcional)
 * @param array|null $dados_novos Dados após a alteração (opcional)
 */
function registrarLog($acao, $modulo, $descricao, $registro_id = null, $dados_anteriores = null, $dados_novos = null) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        $sql = "INSERT INTO logs_sistema (
            usuario_id, usuario_nome, acao, modulo, descricao,
            registro_id, dados_anteriores, dados_novos, ip, user_agent, data_hora
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_SESSION['usuario_id'] ?? null,
            $_SESSION['usuario_nome'] ?? 'Sistema',
            $acao,
            $modulo,
            $descricao,
            $registro_id,
            $dados_anteriores ? json_encode($dados_anteriores, JSON_UNESCAPED_UNICODE) : null,
            $dados_novos ? json_encode($dados_novos, JSON_UNESCAPED_UNICODE) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

    } catch (Exception $e) {
        error_log("Erro ao registrar log: " . $e->getMessage());
    }
}
