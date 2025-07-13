<?php
// Arquivo de depuração para entender o problema
file_put_contents('debug_guia.txt', "Parâmetros recebidos: " . print_r($_GET, true) . "\n\nPOST: " . print_r($_POST, true) . "\n\nSESSION: " . print_r($_SESSION, true));

// Exibe uma página simples
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug - Gerar Guia</title>
</head>
<body>
    <h1>Depuração - Geração de Guia</h1>
    <p>Recebendo os seguintes parâmetros:</p>
    <pre><?php print_r($_GET); ?></pre>
    
    <h2>Criar guia manualmente</h2>
    <form action="index.php?module=especialidades&action=gerar_guia" method="post">
        <input type="hidden" name="procedimento_id" value="<?php echo $_GET['procedimento_id']; ?>">
        <input type="hidden" name="paciente_id" value="<?php echo $_GET['paciente_id']; ?>">
        <input type="hidden" name="data_agendamento" value="<?php echo str_replace('/', '-', $_GET['data_agendamento']); ?>">
        <input type="hidden" name="horario_agendamento" value="<?php echo $_GET['horario_agendamento']; ?>">
        <input type="hidden" name="observacoes" value="">
        <button type="submit">Gerar Guia Manualmente</button>
    </form>
    
    <h2>Testar diretamente URL formatada</h2>
    <p>
        <a href="index.php?module=especialidades&action=gerar_guia&procedimento_id=<?php echo $_GET['procedimento_id']; ?>&paciente_id=<?php echo $_GET['paciente_id']; ?>&data_agendamento=<?php echo date('Y-m-d', strtotime(str_replace('/', '-', $_GET['data_agendamento']))); ?>&horario_agendamento=<?php echo $_GET['horario_agendamento']; ?>">
            Testar com data formatada
        </a>
    </p>
</body>
</html>