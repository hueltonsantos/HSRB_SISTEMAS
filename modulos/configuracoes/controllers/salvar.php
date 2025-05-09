<?php
$configuracaoModel = new ConfiguracaoModel();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?modulo=configuracoes&action=index');
    exit;
}

// Remove o token CSRF e outros campos que não são configurações
$dados = $_POST;
unset($dados['csrf_token']);

// Processa arquivos se houver
if (isset($_FILES) && !empty($_FILES)) {
    foreach ($_FILES as $chave => $arquivo) {
        if ($arquivo['error'] == 0) {
            // Define o diretório de upload
            $diretorio = __DIR__ . '/../../../../uploads/';
            
            // Cria o diretório se não existir
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            // Nome do arquivo
            $nomeArquivo = time() . '_' . $arquivo['name'];
            $caminhoCompleto = $diretorio . $nomeArquivo;
            
            // Move o arquivo
            if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                // Atualiza o valor no array de dados
                $dados[$chave] = $nomeArquivo;
            }
        } else {
            // Se houver erro, mantém o valor atual
            unset($dados[$chave]);
        }
    }
}

// Atualiza as configurações
$resultado = $configuracaoModel->atualizarConfiguracoes($dados);

if ($resultado) {
    $_SESSION['sucesso'] = 'Configurações atualizadas com sucesso!';
} else {
    $_SESSION['erro'] = 'Erro ao atualizar configurações. Tente novamente.';
}

header('Location: index.php?modulo=configuracoes&action=index');
exit;
?>