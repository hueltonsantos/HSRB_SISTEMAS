<?php
/**
 * Configuração de Vínculos de Profissionais
 */

if (!hasPermission('minha_clinica_config') && !hasPermission('master_profissionais')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();

// Processar Formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profissionalId = $_POST['profissional_id'] ?? null;
    $usuarioId = !empty($_POST['usuario_id']) ? $_POST['usuario_id'] : null;
    $repasse = str_replace(',', '.', $_POST['repasse_padrao']);

    if ($profissionalId) {
        $dados = [
            'profissional_id' => $profissionalId,
            'usuario_sistema_id' => $usuarioId,
            'repasse_padrao_percentual' => (float) $repasse,
            'data_inicio_vinculo' => date('Y-m-d')
        ];

        // Verifica se já existe config para este profissional
        $existe = $db->fetchOne("SELECT id FROM master_profissionais_config WHERE profissional_id = ?", [$profissionalId]);

        try {
            if ($existe) {
                $db->update('master_profissionais_config', $dados, 'id = ?', [$existe['id']]);
            } else {
                $db->insert('master_profissionais_config', $dados);
            }
            $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Configuração salva com sucesso!'];
        } catch (Exception $e) {
            $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Erro ao salvar: ' . $e->getMessage()];
        }
    }

    header('Location: index.php?module=minha_clinica&action=config_profissionais');
    exit;
}

// Listar Profissionais com suas Configurações
// Listar Profissionais com suas Configurações
$sql = "SELECT p.id, p.nome, p.registro_profissional,
               pc.repasse_padrao_percentual, pc.usuario_sistema_id,
               u.nome as usuario_nome
        FROM master_profissionais p
        LEFT JOIN master_profissionais_config pc ON p.id = pc.profissional_id
        LEFT JOIN usuarios u ON pc.usuario_sistema_id = u.id
        ORDER BY p.nome";

$profissionais = [];
try {
    $profissionais = $db->fetchAll($sql);
    if ($profissionais === false)
        $profissionais = [];
} catch (Exception $e) {
    echo "Erro SQL: " . $e->getMessage();
    exit;
}

// Listar Usuários Disponíveis (para o select)
$usuarios = [];
try {
    $usuarios = $db->fetchAll("SELECT id, nome, email FROM usuarios WHERE status = 1 ORDER BY nome");
    if ($usuarios === false)
        $usuarios = [];
} catch (Exception $e) {
    // Silently fail or log
}

$pageTitle = 'Configuração de Profissionais';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/profissionais/config.php';
