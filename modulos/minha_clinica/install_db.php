<?php
// Script para executar a migração de banco de dados
// Deve ser rodado via linha de comando ou browser (protegido)

define('BASE_PATH', __DIR__ . '/../../');

if (!file_exists(BASE_PATH . 'config.php') || !file_exists(BASE_PATH . 'Database.php') || !file_exists(BASE_PATH . 'Model.php')) {
    die("Erro: Não foi possível localizar os arquivos de configuração do sistema.");
}

// Carregar dependências na ordem correta
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'Database.php';
require_once BASE_PATH . 'Model.php';

class MigrationRunner extends Model {
    public function run($sqlFile) {
        if (!file_exists($sqlFile)) {
            echo "Arquivo SQL não encontrado: $sqlFile\n";
            return false;
        }

        $sqlContent = file_get_contents($sqlFile);
        
        // Remove comentários de linha
        $lines = explode("\n", $sqlContent);
        $cleanSql = "";
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && strpos($line, '--') !== 0) {
                $cleanSql .= $line . "\n";
            }
        }

        // Separa por ; para executar comando a comando
        $commands = explode(';', $cleanSql);

        echo "Iniciando migração...\n";

        foreach ($commands as $cmd) {
            $cmd = trim($cmd);
            if (!empty($cmd)) {
                try {
                    $this->db->query($cmd);
                    echo "Comando executado com sucesso: " . substr($cmd, 0, 50) . "...\n";
                } catch (PDOException $e) {
                    // Ignora erro de coluna já existente (Duplicate column name)
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        echo "Aviso: Coluna já existe (ignorado).\n";
                    } elseif (strpos($e->getMessage(), 'already exists') !== false) {
                         echo "Aviso: Tabela ou coluna já existe (ignorado).\n";
                    } else {
                        echo "ERRO ao executar comando: " . $e->getMessage() . "\n";
                        echo "SQL: $cmd\n";
                    }
                }
            }
        }
        echo "Migração concluída.\n";
    }
}

// Instancia e roda
$migration = new MigrationRunner();
$migration->run(__DIR__ . '/sql/001_full_migration.sql');
?>
