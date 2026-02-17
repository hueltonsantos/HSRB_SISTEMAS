<?php

/**
 * API para operacoes AJAX do Kanban
 */

header('Content-Type: application/json');

// Verificar se usuario tem permissao
if (!function_exists('hasPermission') || !hasPermission('kanban_view')) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$model = new KanbanModel();
$action = $_POST['api_action'] ?? $_GET['api_action'] ?? '';

try {
    switch ($action) {
        // ==================== COLUNAS ====================
        case 'add_coluna':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $id = $model->salvarColuna([
                'quadro_id' => (int) $_POST['quadro_id'],
                'nome' => trim($_POST['nome']),
                'cor' => $_POST['cor'] ?? '#858796'
            ]);
            $coluna = $model->buscarColuna($id);
            echo json_encode(['success' => true, 'coluna' => $coluna]);
            break;

        case 'update_coluna':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $model->salvarColuna([
                'id' => (int) $_POST['id'],
                'nome' => trim($_POST['nome']),
                'cor' => $_POST['cor'] ?? '#858796',
                'limite_cards' => $_POST['limite_cards'] ?? null
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_coluna':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $model->excluirColuna((int) $_POST['id']);
            echo json_encode(['success' => true]);
            break;

        case 'reorder_colunas':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $ordens = json_decode($_POST['ordens'], true);
            $model->reordenarColunas((int) $_POST['quadro_id'], $ordens);
            echo json_encode(['success' => true]);
            break;

        // ==================== CARDS ====================
        case 'add_card':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $id = $model->salvarCard([
                'coluna_id' => (int) $_POST['coluna_id'],
                'titulo' => trim($_POST['titulo']),
                'descricao' => $_POST['descricao'] ?? '',
                'cor_etiqueta' => $_POST['cor_etiqueta'] ?? null,
                'prioridade' => $_POST['prioridade'] ?? 'media',
                'responsavel_id' => $_POST['responsavel_id'] ?: null,
                'data_vencimento' => $_POST['data_vencimento'] ?: null,
                'criado_por' => $_SESSION['usuario_id']
            ]);
            $card = $model->buscarCard($id);
            $model->registrarHistorico($id, $_SESSION['usuario_id'], 'criado');
            echo json_encode(['success' => true, 'card' => $card]);
            break;

        case 'update_card':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $model->salvarCard([
                'id' => (int) $_POST['id'],
                'titulo' => trim($_POST['titulo']),
                'descricao' => $_POST['descricao'] ?? '',
                'cor_etiqueta' => $_POST['cor_etiqueta'] ?? null,
                'prioridade' => $_POST['prioridade'] ?? 'media',
                'responsavel_id' => $_POST['responsavel_id'] ?: null,
                'data_vencimento' => $_POST['data_vencimento'] ?: null
            ]);
            $model->registrarHistorico((int) $_POST['id'], $_SESSION['usuario_id'], 'editado');
            echo json_encode(['success' => true]);
            break;

        case 'move_card':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $model->moverCard(
                (int) $_POST['card_id'],
                (int) $_POST['coluna_destino_id'],
                (int) $_POST['ordem'],
                $_SESSION['usuario_id']
            );
            echo json_encode(['success' => true]);
            break;

        case 'reorder_cards':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $ordens = json_decode($_POST['ordens'], true);
            $model->reordenarCards((int) $_POST['coluna_id'], $ordens);
            echo json_encode(['success' => true]);
            break;

        case 'delete_card':
            if (!hasPermission('kanban_manage')) throw new Exception('Sem permissao');
            $model->excluirCard((int) $_POST['id']);
            echo json_encode(['success' => true]);
            break;

        case 'get_card':
            $card = $model->buscarCard((int) $_GET['id']);
            $card['comentarios'] = $model->listarComentarios($card['id']);
            $card['historico'] = $model->listarHistorico($card['id']);
            echo json_encode(['success' => true, 'card' => $card]);
            break;

        // ==================== COMENTARIOS ====================
        case 'add_comentario':
            $id = $model->adicionarComentario(
                (int) $_POST['card_id'],
                $_SESSION['usuario_id'],
                trim($_POST['comentario'])
            );
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        // ==================== ESTATISTICAS ====================
        case 'stats':
            $stats = $model->estatisticasQuadro((int) $_GET['quadro_id']);
            echo json_encode(['success' => true, 'stats' => $stats]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acao desconhecida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
