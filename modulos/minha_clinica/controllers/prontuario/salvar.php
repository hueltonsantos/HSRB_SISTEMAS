<?php
/**
 * Salvar Evolução
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (function_exists('hasPermission') && !hasPermission('prontuario_paciente') && !hasPermission('minha_clinica_pacientes') && !hasPermission('painel_profissional')) {
    die('Acesso negado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance();

    // Identificar Profissional (Segurança)
    $sqlProf = "SELECT p.id FROM master_profissionais_config pc 
                JOIN master_profissionais p ON pc.profissional_id = p.id
                WHERE pc.usuario_sistema_id = ?";
    $prof = $db->fetchOne($sqlProf, [$_SESSION['usuario_id']]);

    if (!$prof) {
        die("Perfil profissional não encontrado.");
    }

    require_once MINHA_CLINICA_PATH . '/models/ProntuarioModel.php';
    $model = new ProntuarioModel();

    $dados = [
        'paciente_id' => $_POST['paciente_id'],
        'profissional_id' => $prof['id'],
        'agendamento_id' => $_POST['agendamento_id'],
        'texto' => $_POST['texto'],
        'cid10' => $_POST['cid10'] ?? null,
        'id_anterior' => $_POST['id_anterior'] ?? null // Para versionamento
    ];

    try {
        $model->salvarEvolucao($dados);

        // Marcar agendamento como 'realizado'
        $db->update('master_agendamentos', ['status' => 'realizado'], 'id = ?', [$dados['agendamento_id']]);

        $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Evolução registrada com sucesso!'];
        header('Location: index.php?module=minha_clinica&action=painel_profissional');
        exit;

    } catch (Exception $e) {
        $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Erro ao salvar: ' . $e->getMessage()];
        header('Location: index.php?module=minha_clinica&action=prontuario_paciente&agendamento_id=' . $dados['agendamento_id']);
        exit;
    }
}
