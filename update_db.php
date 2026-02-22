<?php
/**
 * Script para aplicar migrações de banco de dados
 */

// Define constants expected by config.php if not running via web
if (php_sapi_name() === 'cli') {
    // Determine root path
    define('ROOT_PATH', __DIR__);
}

require_once 'config.php';
require_once 'Database.php';

echo "Iniciando atualização do banco de dados...\n";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $sqlFile = __DIR__ . '/Banco_sql/migration_v13_add_obs_clinicas.sql';

    if (!file_exists($sqlFile)) {
        die("Arquivo SQL não encontrado: $sqlFile\n");
    }

    $sql = file_get_contents($sqlFile);

    // Split by semicolon to execute/prepare statements individually if needed, 
    // but PDO execute might handle multiple if allowed. 
    // Safer to split.
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            echo "Executando: " . substr($stmt, 0, 50) . "...\n";
            $conn->exec($stmt);
        }
    }

    echo "Atualização concluída com sucesso!\n";

} catch (Exception $e) {
    die("Erro ao atualizar banco de dados: " . $e->getMessage() . "\n");
}
?>