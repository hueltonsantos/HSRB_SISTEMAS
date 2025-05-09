<?php
// Arquivo para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carregar configurações
require_once 'config.php';

echo "<h2>Informações do Sistema</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Database Config: " . DB_HOST . " / " . DB_NAME . "</p>";

echo "<h2>Teste de Conexão</h2>";
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color:green'>Conexão estabelecida com sucesso!</p>";
    
    $stmt = $pdo->query("SHOW TABLES");
    echo "<p>Tabelas no banco:</p><ul>";
    while ($row = $stmt->fetch()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    
    $stmt = $pdo->query("DESCRIBE usuarios");
    echo "<p>Estrutura da tabela 'usuarios':</p><table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erro de conexão: " . $e->getMessage() . "</p>";
}

echo "<h2>Informações de Módulos</h2>";
echo "<p>Verificando estrutura de diretórios:</p><ul>";
$directories = [
    'modulos/usuarios',
    'modulos/usuarios/controllers',
    'modulos/usuarios/models',
    'modulos/usuarios/templates'
];
foreach ($directories as $dir) {
    echo "<li>" . $dir . ": " . (is_dir($dir) ? "Existe" : "Não existe") . "</li>";
}
echo "</ul>";

echo "<h2>Teste de Formulário</h2>";
echo "<p>Use este formulário para testar a inserção direta:</p>";
?>
<form method="post" action="debug.php">
    <input type="hidden" name="debug_action" value="test_insert">
    <input type="text" name="nome" placeholder="Nome" value="Teste Debug"><br>
    <input type="email" name="email" placeholder="Email" value="debug@teste.com"><br>
    <input type="password" name="senha" placeholder="Senha" value="123456"><br>
    <select name="nivel_acesso">
        <option value="admin">Admin</option>
        <option value="recepcionista">Recepcionista</option>
        <option value="medico">Médico</option>
    </select><br>
    <input type="checkbox" name="status" value="1" checked> Ativo<br>
    <button type="submit">Testar Inserção</button>
</form>

<?php
// Teste de inserção direta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['debug_action']) && $_POST['debug_action'] === 'test_insert') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $nivel_acesso = $_POST['nivel_acesso'];
    $status = isset($_POST['status']) ? 1 : 0;
    
    echo "<h3>Dados recebidos:</h3>";
    echo "<pre>";
    echo "Nome: $nome\n";
    echo "Email: $email\n";
    echo "Nivel: $nivel_acesso\n";
    echo "Status: $status\n";
    echo "</pre>";
    
    try {
        $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$nome, $email, $senha, $nivel_acesso, $status]);
        
        if ($resultado) {
            $id = $pdo->lastInsertId();
            echo "<p style='color:green'>SUCESSO! Usuário inserido com ID: $id</p>";
        } else {
            echo "<p style='color:red'>FALHA ao inserir usuário.</p>";
            print_r($stmt->errorInfo());
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>ERRO: " . $e->getMessage() . "</p>";
    }
}
?>