<?php
require_once 'auth.php';
verificar_acesso('user_manage'); // Updated permission

// Fetch data for dropdowns
$db = Database::getInstance();
$perfis = $db->fetchAll("SELECT * FROM perfis WHERE status=1 ORDER BY nome");
$clinicas = $db->fetchAll("SELECT * FROM clinicas_parceiras WHERE status=1 ORDER BY nome");
$supervisores = $db->fetchAll("SELECT * FROM usuarios WHERE status=1 ORDER BY nome");

// Carrega o template de formulário para novo usuário
require_once USUARIOS_TEMPLATE_PATH . 'formulario.php';
?>