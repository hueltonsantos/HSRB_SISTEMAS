<?php
/**
 * Dashboard do Profissional - Minha Agenda
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar permissao
if (function_exists('hasPermission') && !hasPermission('painel_profissional')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();
$usuarioId = $_SESSION['usuario_id'];

// 1. Identificar Profissional Vinculado
$sqlProf = "SELECT pc.*, p.nome, p.id as profissional_id_real 
            FROM master_profissionais_config pc
            JOIN master_profissionais p ON pc.profissional_id = p.id
            WHERE pc.usuario_sistema_id = ? AND pc.ativo = 1";
$profissionalConfig = $db->fetchOne($sqlProf, [$usuarioId]);

if (!$profissionalConfig) {
    // Se não for profissional, talvez redirecionar ou mostrar erro
    // Por enquanto, mostra mensagem
    $erroperfil = "Seu usuário não está vinculado a um cadastro de profissional ativo.";
    require_once MINHA_CLINICA_TEMPLATES_PATH . '/painel/erro.php'; // Criar esse template simples
    exit;
}

$profissionalId = $profissionalConfig['profissional_id_real'];
$nomeProfissional = $profissionalConfig['nome'];

// 2. Buscar Agendamentos de Hoje
$hoje = date('Y-m-d');
$sqlAgenda = "SELECT a.*, 
                     p.nome as paciente_nome, p.data_nascimento,
                     c.nome_fantasia as convenio_nome,
                     g.status as status_guia,
                     (SELECT GROUP_CONCAT(proc.procedimento SEPARATOR ', ') 
                      FROM master_agendamento_procedimentos map
                      JOIN master_procedimentos proc ON map.procedimento_id = proc.id 
                      WHERE map.agendamento_id = a.id) as procedimentos_lista
              FROM master_agendamentos a
              JOIN pacientes p ON a.paciente_id = p.id
              LEFT JOIN master_convenios c ON a.convenio_id = c.id
              LEFT JOIN master_guias g ON a.guia_id = g.id
              WHERE a.profissional_id = ? AND a.data_consulta = ?
              ORDER BY a.hora_consulta ASC";

$agendamentos = $db->fetchAll($sqlAgenda, [$profissionalId, $hoje]);

$pageTitle = 'Painel do Profissional - ' . date('d/m/Y');
require_once MINHA_CLINICA_TEMPLATES_PATH . '/painel/dashboard.php';
