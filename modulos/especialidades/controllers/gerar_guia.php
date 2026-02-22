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
$procedimentoId = (int) $_REQUEST['procedimento_id'];
$agendamentoId = isset($_REQUEST['agendamento_id']) ? (int) $_REQUEST['agendamento_id'] : null;

try {
    // Conecta ao banco de dados usando as configurações do sistema
    $db = Database::getInstance()->getConnection();


<<<<<<< HEAD
    // Busca procedimentos do agendamento se houver
    $procedimentosAgendamento = [];
    $clinicaData = null;

    if ($agendamentoId) {
        $stmtProc = $db->prepare("
            SELECT vp.procedimento, e.nome as especialidade_nome, c.nome as clinica_nome, 
                   c.endereco, c.telefone
            FROM agendamento_procedimentos ap
            JOIN valores_procedimentos vp ON ap.procedimento_id = vp.id
            JOIN especialidades e ON vp.especialidade_id = e.id
            JOIN agendamentos a ON ap.agendamento_id = a.id
            LEFT JOIN clinicas_parceiras c ON a.clinica_id = c.id
            WHERE ap.agendamento_id = ?
        ");
        $stmtProc->execute([$agendamentoId]);
        $procedimentosAgendamento = $stmtProc->fetchAll(PDO::FETCH_ASSOC);

        // Pega dados da clínica do primeiro procedimento para exibir
        if (!empty($procedimentosAgendamento)) {
            $clinicaData = [
                'clinica_nome' => $procedimentosAgendamento[0]['clinica_nome'],
                'endereco' => $procedimentosAgendamento[0]['endereco'],
                'telefone' => $procedimentosAgendamento[0]['telefone']
            ];
        }
    }

    // Se não tiver procedimentos via agendamento, busca o individual (fallback/comportamento original)
    if (empty($procedimentosAgendamento)) {
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

        $procedimentosAgendamento[] = [
            'procedimento' => $procedimento['procedimento'],
            'especialidade_nome' => $procedimento['especialidade_nome'],
            'clinica_nome' => $procedimento['clinica_nome'],
            'endereco' => $procedimento['endereco'],
            'telefone' => $procedimento['telefone']
        ];

        $clinicaData = $procedimento;
    } else {
        // Mock do array $procedimento para compatibilidade com o resto do código que espera ele
        $procedimento = [
            'procedimento' => $procedimentosAgendamento[0]['procedimento'],
            'especialidade_nome' => $procedimentosAgendamento[0]['especialidade_nome'],
            'clinica_nome' => $clinicaData['clinica_nome'],
            'endereco' => $clinicaData['endereco'],
            'telefone' => $clinicaData['telefone'],
            'especialidade_id' => 0 // Mock, não usado criticamente na view
        ];
    }
=======
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
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750

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

<<<<<<< HEAD
        $agendamentoId = isset($_REQUEST['agendamento_id']) ? (int) $_REQUEST['agendamento_id'] : (isset($_GET['agendamento_id']) ? (int) $_GET['agendamento_id'] : null);

=======
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        // Verificar se tabela existe, se não criar
        try {
            $db->exec("
                CREATE TABLE IF NOT EXISTS `guias_encaminhamento` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `paciente_id` int(11) NOT NULL,
<<<<<<< HEAD
                    `agendamento_id` int(11) DEFAULT NULL,
=======
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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
<<<<<<< HEAD

            // Tenta adicionar a coluna se ela não existir (para tabelas antigas)
            try {
                $db->exec("ALTER TABLE guias_encaminhamento ADD COLUMN agendamento_id INT(11) NULL AFTER paciente_id");
            } catch (Exception $e) {
                // Coluna já existe, ignora
            }

=======
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
        } catch (Exception $tableError) {
            // Ignora se já existe
        }

        // Salvar a guia no banco
        $stmtSalvarGuia = $db->prepare("
            INSERT INTO guias_encaminhamento (
<<<<<<< HEAD
                paciente_id, agendamento_id, procedimento_id, data_agendamento,
=======
                paciente_id, procedimento_id, data_agendamento,
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                horario_agendamento, observacoes, status,
                data_emissao, codigo
            ) VALUES (
                ?, ?, ?, ?, ?, ?, 'agendado', NOW(), ?
            )
        ");

        $stmtSalvarGuia->execute([
            $pacienteId,
            $agendamentoId,
            $procedimentoId,
            $dataAgendamento,
            $horarioAgendamento,
            $observacoes,
            $codigo
        ]);

        $guiaId = $db->lastInsertId();

        // Limpar todos os buffers de saída
        while (ob_get_level()) {
            ob_end_clean();
        }

<<<<<<< HEAD
        // Redirecionar para o controlador de impressão que já trata múltiplos procedimentos corretamente
        header("Location: index.php?module=guias&action=print&id={$guiaId}");
=======
        // Limpar todos os buffers de saída antes de renderizar a guia
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Renderizar o template da guia para impressão
        include __DIR__ . '/../templates/guia_impressao.php';
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
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
