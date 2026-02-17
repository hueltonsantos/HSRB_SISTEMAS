<?php

/**
 * Controlador - Salvar Quadro Kanban
 */

verificar_acesso('kanban_manage');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?module=kanban');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$cor = $_POST['cor'] ?? '#4e73df';

if (empty($nome)) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Nome do quadro e obrigatorio'];
    header('Location: index.php?module=kanban&action=' . ($id ? 'editar_quadro&id=' . $id : 'novo_quadro'));
    exit;
}

$model = new KanbanModel();

$dados = [
    'nome' => $nome,
    'descricao' => $descricao,
    'cor' => $cor,
    'criado_por' => $_SESSION['usuario_id']
];

if ($id) {
    $dados['id'] = $id;
}

$quadroId = $model->salvarQuadro($dados);

// Se for novo quadro, criar colunas padrao
if (!$id) {
    $colunasPadrao = [
        ['nome' => 'A Fazer', 'cor' => '#858796'],
        ['nome' => 'Em Andamento', 'cor' => '#f6c23e'],
        ['nome' => 'Concluido', 'cor' => '#1cc88a']
    ];

    foreach ($colunasPadrao as $coluna) {
        $model->salvarColuna([
            'quadro_id' => $quadroId,
            'nome' => $coluna['nome'],
            'cor' => $coluna['cor']
        ]);
    }

    registrarLog('criar', 'kanban', "Quadro '$nome' criado", $quadroId);
} else {
    registrarLog('editar', 'kanban', "Quadro '$nome' atualizado", $quadroId);
}

$_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Quadro salvo com sucesso!'];
header('Location: index.php?module=kanban&action=quadro&id=' . $quadroId);
exit;
