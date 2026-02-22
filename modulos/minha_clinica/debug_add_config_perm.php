<?php
define('ROOT_PATH', __DIR__ . '/../../');
require_once ROOT_PATH . 'config.php';
require_once ROOT_PATH . 'Database.php';

$db = Database::getInstance();
$perm = 'minha_clinica_config';

$check = $db->fetchOne("SELECT * FROM permissoes WHERE chave = ?", [$perm]);
if ($check) {
    echo "Permission '$perm' exists.\n";
} else {
    echo "Permission '$perm' does NOT exist. Adding it...\n";
    try {
        $db->insert('permissoes', [
            'nome' => 'Configurar Profissionais',
            'chave' => $perm,
            'descricao' => 'Permite vincular usuários a profissionais e definir repasses'
        ]);
        echo "Added.\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
