<?php
// Verifica se o acesso é direto
if (!defined('BASEPATH')) exit('Acesso negado');

// Prepara a consulta SQL
$query = "SELECT 
            vp.id AS procedimento_id,
            vp.procedimento,
            vp.valor AS valor_procedimento,
            e.id AS especialidade_id,
            e.nome AS especialidade,
            cp.id AS clinica_id,
            cp.nome AS clinica,
            cp.endereco,
            cp.numero,
            cp.bairro,
            cp.cidade,
            cp.estado
        FROM 
            valores_procedimentos vp
        LEFT JOIN 
            especialidades e ON vp.especialidade_id = e.id
        LEFT JOIN 
            especialidades_clinicas ec ON e.id = ec.especialidade_id
        LEFT JOIN 
            clinicas_parceiras cp ON ec.clinica_id = cp.id
        WHERE
            vp.status = 1 
            AND (cp.id IS NULL OR cp.status = 1)";

// Adiciona filtros se existirem
$params = [];
if (!empty($_GET['procedimento'])) {
    $query .= " AND vp.procedimento LIKE ?";
    $params[] = '%' . $_GET['procedimento'] . '%';
}
if (!empty($_GET['especialidade_id'])) {
    $query .= " AND e.id = ?";
    $params[] = $_GET['especialidade_id'];
}
if (!empty($_GET['clinica_id'])) {
    $query .= " AND cp.id = ?";
    $params[] = $_GET['clinica_id'];
}

$query .= " ORDER BY vp.procedimento, cp.nome";

// Executa a consulta
$resultados = $db->query($query, $params);

// Consultas para filtros
$especialidades = $db->query("SELECT * FROM especialidades WHERE status = 1 ORDER BY nome");
$clinicas = $db->query("SELECT * FROM clinicas_parceiras WHERE status = 1 ORDER BY nome");

// Valores para filtros
$filtroProcedimento = isset($_GET['procedimento']) ? $_GET['procedimento'] : '';
$filtroEspecialidade = isset($_GET['especialidade_id']) ? $_GET['especialidade_id'] : '';
$filtroClinica = isset($_GET['clinica_id']) ? $_GET['clinica_id'] : '';
?>

<div class="container-fluid">
    <h1 class="mt-4">Consulta de Procedimentos e Preços</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Tabela de Preços</li>
    </ol>
    
    <!-- Formulário de filtro -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search mr-1"></i> Pesquisar Procedimentos
        </div>
        <div class="card-body">
            <form method="get" action="index.php">
                <input type="hidden" name="module" value="tabela_precos">
                
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="procedimento">Nome do Procedimento:</label>
                        <input type="text" class="form-control" id="procedimento" name="procedimento" 
                               value="<?= htmlspecialchars($filtroProcedimento) ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="especialidade_id">Especialidade:</label>
                        <select class="form-control" id="especialidade_id" name="especialidade_id">
                            <option value="">Todas as Especialidades</option>
                            <?php while ($e = $especialidades->fetch_assoc()): ?>
                                <option value="<?= $e['id'] ?>" <?= $filtroEspecialidade == $e['id'] ? 'selected' : '' ?>>
                                    <?= $e['nome'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="clinica_id">Clínica:</label>
                        <select class="form-control" id="clinica_id" name="clinica_id">
                            <option value="">Todas as Clínicas</option>
                            <?php while ($c = $clinicas->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>" <?= $filtroClinica == $c['id'] ? 'selected' : '' ?>>
                                    <?= $c['nome'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Pesquisar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabela de resultados -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i> Procedimentos e Locais de Atendimento
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Procedimento</th>
                            <th>Especialidade</th>
                            <th>Valor</th>
                            <th>Clínica</th>
                            <th>Endereço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($resultados && $resultados->num_rows > 0) {
                            while ($row = $resultados->fetch_assoc()) {
                                // Só mostra linhas que têm clínica associada
                                if (!empty($row['clinica_id'])):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['procedimento']) ?></td>
                                <td><?= htmlspecialchars($row['especialidade']) ?></td>
                                <td class="text-right">R$ <?= number_format($row['valor_procedimento'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['clinica']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['endereco']) ?>
                                    <?= !empty($row['numero']) ? ', ' . htmlspecialchars($row['numero']) : '' ?>
                                    <?= !empty($row['bairro']) ? ' - ' . htmlspecialchars($row['bairro']) : '' ?><br>
                                    <?= htmlspecialchars($row['cidade']) ?>/<?= htmlspecialchars($row['estado']) ?>
                                </td>
                            </tr>
                        <?php 
                                endif;
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum procedimento encontrado com os critérios de pesquisa.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializa o DataTables
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        pageLength: 25
    });
});
</script>