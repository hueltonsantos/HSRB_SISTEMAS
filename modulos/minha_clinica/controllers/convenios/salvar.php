<?php
/**
 * Salvar Convênio
 */

if (!hasPermission('minha_clinica_editar')) {
    header('Location: acesso_negado.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = new ConveniosModel();
    
    $dados = [
        'id' => isset($_POST['id']) ? (int)$_POST['id'] : null,
        'nome_fantasia' => $_POST['nome_fantasia'],
        'razao_social' => $_POST['razao_social'],
        'cnpj' => $_POST['cnpj'],
        'registro_ans' => $_POST['registro_ans'],
        'dias_retorno' => (int)$_POST['dias_retorno'],
        'prazo_recebimento_dias' => (int)$_POST['prazo_recebimento_dias'],
        'ativo' => isset($_POST['ativo']) ? 1 : 0
    ];

    try {
        $id = $model->salvar($dados);
        
        $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Convênio salvo com sucesso!'];
        header('Location: index.php?module=minha_clinica&action=convenios');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Erro ao salvar: ' . $e->getMessage()];
        header('Location: index.php?module=minha_clinica&action=convenios');
        exit;
    }
}
