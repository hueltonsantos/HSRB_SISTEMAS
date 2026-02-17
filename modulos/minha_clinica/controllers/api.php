<?php
/**
 * API - Minha Clinica
 */

header('Content-Type: application/json');

$model = new MinhaClinicaModel();
$apiAction = $_GET['api_action'] ?? $_GET['action'] ?? '';

switch ($apiAction) {
    case 'get_procedimentos':
        $especialidadeId = $_GET['especialidade_id'] ?? null;
        if ($especialidadeId) {
            $procedimentos = $model->getProcedimentos($especialidadeId, true);
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
        $todosHorarios = [];
        for ($h = 8; $h <= 18; $h++) {
            $todosHorarios[] = sprintf('%02d:00:00', $h);
            if ($h < 18) {
                $todosHorarios[] = sprintf('%02d:30:00', $h);
            }
        }

        $disponiveis = array_diff($todosHorarios, $ocupados);
        echo json_encode(array_values($disponiveis));
        break;

    case 'alterar_status':
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id && $status) {
            $result = $model->atualizarStatusAgendamento($id, $status);
            if ($result) {
                $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Status atualizado com sucesso!'];
            } else {
                $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Erro ao atualizar status'];
            }
        }
        header('Location: index.php?module=minha_clinica&action=agendamentos');
        exit;
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
