<?php

/**
 * Controlador para gerar guia de encaminhamento
 */

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Garantir que Database está carregado
if (!class_exists('Database')) {
    require_once __DIR__ . '/../../../Database.php';
}

// Verifica se os parâmetros necessários foram informados
if (!isset($_GET['procedimento_id']) || empty($_GET['procedimento_id'])) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'ID do procedimento não informado'
    ];

    // Redireciona para a listagem
    header('Location: index.php?module=especialidades&action=list');
    exit;
}

// Obtém o ID do procedimento
$procedimentoId = (int) $_GET['procedimento_id'];

try {
    // Conecta ao banco de dados usando as configurações do sistema
    $db = Database::getInstance()->getConnection();


    // Busca informações do procedimento e de UMA clínica associada (via especialidade)
    $stmt = $db->prepare("
        SELECT p.*, e.nome as especialidade_nome, c.nome as clinica_nome,
               c.endereco, c.telefone
        FROM valores_procedimentos p
        INNER JOIN especialidades e ON p.especialidade_id = e.id
        LEFT JOIN especialidades_clinicas ec ON e.id = ec.especialidade_id
        LEFT JOIN clinicas_parceiras c ON ec.clinica_id = c.id
        WHERE p.id = ?
        LIMIT 1
    ");
    $stmt->execute([$procedimentoId]);
    $procedimento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$procedimento) {
        throw new Exception("Procedimento não encontrado");
    }

    // Busca lista de pacientes para o dropdown
    $stmtPacientes = $db->prepare("SELECT id, nome, cpf FROM pacientes ORDER BY nome");
    $stmtPacientes->execute();
    $pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);

    // Preenche os dados do formulário a partir dos parâmetros da URL
    if (isset($_GET['paciente_id']) && !empty($_GET['paciente_id'])) {
        $pacienteId = (int) $_GET['paciente_id'];
        $dataAgendamento = isset($_GET['data_agendamento']) ? $_GET['data_agendamento'] : date('Y-m-d');
        $horarioAgendamento = isset($_GET['horario_agendamento']) ? $_GET['horario_agendamento'] : '';

        // Formata a data, se necessário (converte de DD/MM/YYYY para YYYY-MM-DD)
        if (strpos($dataAgendamento, '/') !== false) {
            $dateParts = explode('/', $dataAgendamento);
            if (count($dateParts) == 3) {
                $dataAgendamento = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            }
        }

        $_SESSION['form_data'] = [
            'paciente_id' => $pacienteId,
            'data_agendamento' => $dataAgendamento,
            'horario_agendamento' => $horarioAgendamento
        ];
    }

    // Se o método for POST, significa que estamos preenchendo os dados do paciente
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar dados
        if (empty($_POST['paciente_id']) || empty($_POST['data_agendamento'])) {
            $_SESSION['mensagem'] = [
                'tipo' => 'danger',
                'texto' => 'Preencha todos os campos obrigatórios'
            ];
            $_SESSION['form_data'] = $_POST;
            header("Location: index.php?module=especialidades&action=gerar_guia&procedimento_id={$procedimentoId}");
            exit;
        }

        $pacienteId = (int) $_POST['paciente_id'];
        $dataAgendamento = $_POST['data_agendamento'];
        $horarioAgendamento = !empty($_POST['horario_agendamento']) ? $_POST['horario_agendamento'] : null;
        $observacoes = $_POST['observacoes'] ?? '';

        // Verificar se o paciente existe
        $stmtVerificaPaciente = $db->prepare("SELECT id, nome, cpf FROM pacientes WHERE id = ?");
        $stmtVerificaPaciente->execute([$pacienteId]);
        $paciente = $stmtVerificaPaciente->fetch(PDO::FETCH_ASSOC);

        if (!$paciente) {
            throw new Exception("Paciente não encontrado");
        }

        // Gerar código único para a guia (opcional)
        $codigo = 'G' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Verificar se tabela existe, se não criar
        try {
            $db->exec("
                CREATE TABLE IF NOT EXISTS `guias_encaminhamento` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `paciente_id` int(11) NOT NULL,
                    `procedimento_id` int(11) NOT NULL,
                    `data_agendamento` date NOT NULL,
                    `horario_agendamento` time DEFAULT NULL,
                    `observacoes` text DEFAULT NULL,
                    `status` varchar(20) DEFAULT 'agendado',
                    `data_emissao` datetime DEFAULT NULL,
                    `codigo` varchar(20) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        } catch (Exception $tableError) {
            // Ignora se já existe
        }

        // Salvar a guia no banco
        $stmtSalvarGuia = $db->prepare("
            INSERT INTO guias_encaminhamento (
                paciente_id, procedimento_id, data_agendamento,
                horario_agendamento, observacoes, status,
                data_emissao, codigo
            ) VALUES (
                ?, ?, ?, ?, ?, 'agendado', NOW(), ?
            )
        ");

        $stmtSalvarGuia->execute([
            $pacienteId,
            $procedimentoId,
            $dataAgendamento,
            $horarioAgendamento,
            $observacoes,
            $codigo
        ]);

        $guiaId = $db->lastInsertId();

        // Dados da guia para exibição
        $guia = [
            'id' => $guiaId,
            'codigo' => $codigo,
            'procedimento' => $procedimento,
            'paciente_nome' => $paciente['nome'],
            'paciente_documento' => $paciente['cpf'],
            'data_agendamento' => $dataAgendamento,
            'horario_agendamento' => $horarioAgendamento,
            'observacoes' => $observacoes,
            'data_emissao' => date('d/m/Y')
        ];

        // Limpar todos os buffers de saída antes de renderizar a guia
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Renderizar o template da guia para impressão
        include __DIR__ . '/../templates/guia_impressao.php';
        exit;
    }

    // Se chegou aqui, exibir o formulário para preencher dados do paciente
    include MODULES_PATH . '/especialidades/templates/gerar_guia.php';
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao gerar guia: ' . $e->getMessage()
    ];

    header('Location: index.php?module=especialidades&action=list');
    exit;
}
