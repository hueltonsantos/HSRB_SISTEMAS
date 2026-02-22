<?php
/**
 * Relatório de Repasses Financeiros
 */

if (!hasPermission('minha_clinica_financeiro')) {
    header('Location: acesso_negado.php');
    exit;
}

$db = Database::getInstance();

// Filtros
$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim = $_GET['data_fim'] ?? date('Y-m-t');
$profissionalId = $_GET['profissional_id'] ?? '';

// 1. Buscar dados do Caixa Realizado (Dinheiro que entrou)
//    Vinculado a Profissionais
$sql = "SELECT cr.*, 
               g.numero_guia,
               a.data_consulta, a.id as agendamento_id,
               pac.nome as paciente_nome,
               conv.id as convenio_id, conv.nome_fantasia as convenio_nome,
               prof.id as profissional_id, prof.nome as profissional_nome,
               pc.repasse_padrao_percentual as repasse_padrao,
               
               (SELECT GROUP_CONCAT(proc.id) 
                FROM master_agendamento_procedimentos map 
                JOIN master_procedimentos proc ON map.procedimento_id = proc.id 
                WHERE map.agendamento_id = a.id) as procedimentos_ids,
                
               (SELECT GROUP_CONCAT(proc.procedimento SEPARATOR ', ') 
                FROM master_agendamento_procedimentos map 
                JOIN master_procedimentos proc ON map.procedimento_id = proc.id 
                WHERE map.agendamento_id = a.id) as procedimentos_nomes

        FROM master_financeiro_caixa_realizado cr
        JOIN master_financeiro_caixa_previsto cp ON cr.previsao_id = cp.id
        LEFT JOIN master_guias g ON cp.guia_id = g.id
        JOIN master_agendamentos a ON cp.agendamento_id = a.id
        JOIN pacientes pac ON a.paciente_id = pac.id
        LEFT JOIN master_convenios conv ON a.convenio_id = conv.id
        JOIN master_profissionais prof ON a.profissional_id = prof.id
        LEFT JOIN master_profissionais_config pc ON prof.id = pc.profissional_id
        
        WHERE cr.data_recebimento BETWEEN ? AND ?
          AND cr.id NOT IN (SELECT caixa_realizado_id FROM master_financeiro_repasses_itens)";

$params = [$dataInicio, $dataFim];

if ($profissionalId) {
    $sql .= " AND prof.id = ?";
    $params[] = $profissionalId;
}

$sql .= " ORDER BY prof.nome, cr.data_recebimento";

$movimentacoes = $db->fetchAll($sql, $params);

// 2. Calcular Repasses
$relatorio = [];
$totalRepasseGeral = 0;

if (!empty($movimentacoes)) {
    foreach ($movimentacoes as $mov) {
        $valorBase = $mov['valor_recebido'];
        
        // Regra de Repasse:
        // 1. Verificar se tem override na tabela de preços (Convênio x Procedimento)
        // Obs: Se tiver multiplos procedimentos, é complexo. 
        // Simplificação: Pega o primeiro procedimento para verificar a regra, 
        // ou usa repasse padrão se tiver muitos.
        
        $percentualAplicado = $mov['repasse_padrao'] ?? 50; // Default 50% se não configurado
        $regraOrigem = 'Padrão Profissional';
        
        // Tenta buscar regra específica se tiver procedimento e convênio
        if ($mov['convenio_id'] && $mov['procedimentos_ids']) {
            $procIds = explode(',', $mov['procedimentos_ids']);
            $procIdPrincipal = $procIds[0]; // Pega o primeiro
            
            $sqlRegra = "SELECT repasse_percentual FROM master_tabela_precos 
                         WHERE convenio_id = ? AND procedimento_id = ? 
                         AND repasse_percentual IS NOT NULL";
            $regraEspecifica = $db->fetchOne($sqlRegra, [$mov['convenio_id'], $procIdPrincipal]);
            
            if ($regraEspecifica) {
                $percentualAplicado = $regraEspecifica['repasse_percentual'];
                $regraOrigem = 'Tabela Específica';
            }
        }
        
        $valorRepasse = $valorBase * ($percentualAplicado / 100);
        $totalRepasseGeral += $valorRepasse;
        
        $mov['valor_repasse'] = $valorRepasse;
        $mov['regra_repasse'] = "$percentualAplicado% ($regraOrigem)";
        
        $relatorio[] = $mov;
    }
}

// 3. Buscar Profissionais para filtro
$profissionais = $db->fetchAll("SELECT id, nome FROM master_profissionais ORDER BY nome");

$pageTitle = 'Relatório de Repasses Médicos';
require_once MINHA_CLINICA_TEMPLATES_PATH . '/financeiro/repasses.php';
