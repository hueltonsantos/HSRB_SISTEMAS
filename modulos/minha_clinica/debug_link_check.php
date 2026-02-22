<?php
define('ROOT_PATH', __DIR__ . '/../../');
require_once ROOT_PATH . 'config.php';
require_once ROOT_PATH . 'Database.php';

$db = Database::getInstance();

echo "=== Usuarios Structure ===\n";
try {
    $desc = $db->fetchAll("DESCRIBE usuarios");
    print_r($desc);
} catch (Exception $e) {
    echo $e->getMessage();
}

echo "\n=== All Users ===\n";
try {
    $users = $db->fetchAll("SELECT * FROM usuarios");
    print_r($users);
} catch (Exception $e) {
    echo $e->getMessage();
}
