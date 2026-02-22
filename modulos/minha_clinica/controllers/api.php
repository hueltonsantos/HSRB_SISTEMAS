<?php
/**
 * API - Minha Clinica
 */

// Limpar buffers do roteador para retornar JSON puro
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

$model = new MinhaClinicaModel();
$apiAction = $_GET['api_action'] ?? $_GET['action'] ?? '';

switch ($apiAction) {
    case 'get_procedimentos':
        $especialidadeId = $_GET['especialidade_id'] ?? null;
        $convenioId = isset($_GET['convenio_id']) && !empty($_GET['convenio_id']) ? (int)$_GET['convenio_id'] : null;

        if ($especialidadeId) {
            $procedimentos = $model->getProcedimentos($especialidadeId, true);
            
            // Se tiver convênio, ajustar valores
            if ($convenioId) {
                require_once MINHA_CLINICA_PATH . '/models/ConveniosModel.php';
                $conveniosModel = new ConveniosModel();
                
                foreach ($procedimentos as &$proc) {
                    $precoInfo = $conveniosModel->getValorExato($convenioId, $proc['id']);
                    $proc['valor'] = $precoInfo['valor'];
                    $proc['codigo'] = $precoInfo['codigo_tuss'];
                }
            }
            
            echo json_encode($procedimentos);
        } else {
            echo json_encode([]);
        }
        break;

    case 'get_profissionais':
        $especialidadeId = $_GET['especialidade_id'] ?? null;
        $profissionais = $model->getProfissionais($especialidadeId, true);
        echo json_encode($profissionais);
        break;

    case 'get_horarios':
        $data = $_GET['data'] ?? date('Y-m-d');
        $profissionalId = $_GET['profissional_id'] ?? null;
        $ocupados = $model->getHorariosOcupados($data, $profissionalId);

        // Gerar horarios disponiveis (8h as 18h, de 30 em 30 min)
        // TODO: Pegar configuração da clínica ou do profissional
        $todosHorarios = [];
        for ($h = 7; $h <= 20; $h++) { // Ampliando horário
            $todosHorarios[] = sprintf('%02d:00:00', $h);
            if ($h < 20) {
                $todosHorarios[] = sprintf('%02d:30:00', $h);
            }
        }

        $disponiveis = array_diff($todosHorarios, $ocupados);
        echo json_encode(array_values($disponiveis));
        break;

    case 'alterar_status':
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        $status = $_POST['status'] ?? $_GET['status'] ?? null;

        if ($id && $status) {
            $result = $model->atualizarStatusAgendamento($id, $status);
            echo json_encode([
                'success' => (bool)$result,
                'message' => $result ? 'Status atualizado com sucesso!' : 'Erro ao atualizar status'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'ID e status são obrigatórios'
            ]);
        }
        break;

    case 'estatisticas':
        $estatisticasHoje = $model->getEstatisticasHoje();
        $estatisticasMes = $model->getEstatisticasMes();
        echo json_encode([
            'hoje' => $estatisticasHoje,
            'mes' => $estatisticasMes
        ]);
        break;

    default:
        echo json_encode(['error' => 'Acao nao reconhecida']);
}

exit;
