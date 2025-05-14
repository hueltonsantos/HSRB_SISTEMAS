<?php
/**
 * Controlador para gerar guia de encaminhamento
 */

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
    // Conecta ao banco de dados
    $db = new PDO('mysql:host=localhost;dbname=clinica_encaminhamento', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Busca informações do procedimento
    $stmt = $db->prepare("
        SELECT p.*, e.nome as especialidade_nome, c.nome as clinica_nome, 
               c.endereco, c.telefone, c.observacoes as clinica_observacoes
        FROM valores_procedimentos p
        INNER JOIN especialidades e ON p.especialidade_id = e.id
        LEFT JOIN clinicas_parceiras c ON e.id = c.especialidade_id
        WHERE p.id = ?
        LIMIT 1
    ");
    $stmt->execute([$procedimentoId]);
    $procedimento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$procedimento) {
        throw new Exception("Procedimento não encontrado");
    }
    
    // Busca lista de pacientes para o dropdown
    $stmtPacientes = $db->prepare("SELECT id, nome, documento FROM pacientes ORDER BY nome");
    $stmtPacientes->execute();
    $pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);
    
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
        $stmtVerificaPaciente = $db->prepare("SELECT id, nome, documento FROM pacientes WHERE id = ?");
        $stmtVerificaPaciente->execute([$pacienteId]);
        $paciente = $stmtVerificaPaciente->fetch(PDO::FETCH_ASSOC);
        
        if (!$paciente) {
            throw new Exception("Paciente não encontrado");
        }
        
        // Gerar código único para a guia (opcional)
        $codigo = 'G' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
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
            'paciente_documento' => $paciente['documento'],
            'data_agendamento' => $dataAgendamento,
            'horario_agendamento' => $horarioAgendamento,
            'observacoes' => $observacoes,
            'data_emissao' => date('d/m/Y')
        ];
        
        // Renderizar o template da guia para impressão
        include ESPECIALIDADES_TEMPLATE_PATH . '/guia_impressao.php';
        exit;
    }
    
    // Se chegou aqui, exibir o formulário para preencher dados do paciente
    include ESPECIALIDADES_TEMPLATE_PATH . '/gerar_guia.php';
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'danger',
        'texto' => 'Erro ao gerar guia: ' . $e->getMessage()
    ];
    
    header('Location: index.php?module=especialidades&action=list');
    exit;
}