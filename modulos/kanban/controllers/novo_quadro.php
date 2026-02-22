<?php

/**
 * Controlador - Novo Quadro Kanban
 */

verificar_acesso('kanban_manage');

$quadro = [
    'id' => '',
    'nome' => '',
    'descricao' => '',
    'cor' => '#4e73df'
];

include KANBAN_TEMPLATE_PATH . '/form_quadro.php';
