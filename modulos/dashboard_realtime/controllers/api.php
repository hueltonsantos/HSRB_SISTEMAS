<?php
/**
 * API AJAX para o Dashboard em Tempo Real
 * Retorna dados em JSON para atualização dos gráficos
 */

header('Content-Type: application/json');

// Verificar permissão
if (!hasPermission('dashboard_realtime')) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$model = new DashboardRealtimeModel();

// Capturar ação da API
$apiAction = isset($_REQUEST['api_action']) ? $_REQUEST['api_action'] : '';

// Capturar filtros
$filtros = [
    'data_inicio' => isset($_REQUEST['data_inicio']) ? $_REQUEST['data_inicio'] : date('Y-m-d'),
    'data_fim' => isset($_REQUEST['data_fim']) ? $_REQUEST['data_fim'] : date('Y-m-d'),
    'clinica_id' => isset($_REQUEST['clinica_id']) && $_REQUEST['clinica_id'] !== '' ? intval($_REQUEST['clinica_id']) : null,
    'especialidade_id' => isset($_REQUEST['especialidade_id']) && $_REQUEST['especialidade_id'] !== '' ? intval($_REQUEST['especialidade_id']) : null,
    'usuario_id' => isset($_REQUEST['usuario_id']) && $_REQUEST['usuario_id'] !== '' ? intval($_REQUEST['usuario_id']) : null
];

try {
    switch ($apiAction) {
        case 'totais':
            $data = $model->getTotaisFinanceiros($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'por_usuario':
            $data = $model->getAgendamentosPorUsuario($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'por_especialidade':
            $data = $model->getAgendamentosPorEspecialidade($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'por_procedimento':
            $data = $model->getAgendamentosPorProcedimento($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'por_pagamento':
            $data = $model->getDistribuicaoFormaPagamento($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'lista':
            $data = $model->getListaDetalhada($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'all':
            // Carrega todos os dados de uma vez (para carregamento inicial e refresh)
            $allData = $model->getAllData($filtros);
            echo json_encode([
                'success' => true,
                'totais' => $allData['totais'],
                'por_usuario' => $allData['por_usuario'],
                'por_especialidade' => $allData['por_especialidade'],
                'por_procedimento' => $allData['por_procedimento'],
                'por_pagamento' => $allData['por_pagamento'],
                'lista' => $allData['lista']
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida: ' . $apiAction]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}

exit;
