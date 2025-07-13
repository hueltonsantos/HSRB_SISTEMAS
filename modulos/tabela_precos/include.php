<?php
function tabela_precosProcessAction($action) {
    try {
        // Configuração do banco de dados
        $host = 'localhost';
        $dbname = 'clinica_encaminhamento';
        $username = 'root';
        $password = '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Obtém os valores dos filtros
        $filtroProcedimento = isset($_GET['procedimento']) ? $_GET['procedimento'] : '';
        $filtroEspecialidade = isset($_GET['especialidade_id']) ? $_GET['especialidade_id'] : '';
        $filtroClinica = isset($_GET['clinica_id']) ? $_GET['clinica_id'] : '';
        
        // DEPURAÇÃO: Vamos verificar as tabelas antes de continuar
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        // Verificamos as estruturas das tabelas para ter certeza que estamos usando os campos corretos
        $especialidadesFields = [];
        if (in_array('especialidades', $tables)) {
            $stmt = $pdo->query("DESCRIBE especialidades");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $especialidadesFields[] = $row['Field'];
            }
        }
        
        $clinicasFields = [];
        if (in_array('clinicas_parceiras', $tables)) {
            $stmt = $pdo->query("DESCRIBE clinicas_parceiras");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $clinicasFields[] = $row['Field'];
            }
        }
        
        // Consulta SQL base para procedimentos
        $sql = "SELECT 
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
                AND cp.id IS NOT NULL";
        
        // Adiciona condições de filtro
        $params = [];
        if (!empty($filtroProcedimento)) {
            $sql .= " AND vp.procedimento LIKE :procedimento";
            $params[':procedimento'] = "%{$filtroProcedimento}%";
        }
        
        if (!empty($filtroEspecialidade)) {
            $sql .= " AND e.id = :especialidade_id";
            $params[':especialidade_id'] = $filtroEspecialidade;
        }
        
        if (!empty($filtroClinica)) {
            $sql .= " AND cp.id = :clinica_id";
            $params[':clinica_id'] = $filtroClinica;
        }
        
        $sql .= " ORDER BY vp.procedimento";
        
        // Prepara e executa a consulta
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // CONSULTAS PARA FILTROS - CORRIGIDAS
        
        // Consulta para especialidades (com verificação de erro)
        $especialidades = [];
        try {
            $stmtEsp = $pdo->query("SELECT id, nome FROM especialidades WHERE status = 1 ORDER BY nome");
            $especialidades = $stmtEsp->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback se a consulta falhar - verificar campos
            if (in_array('especialidades', $tables)) {
                // Adapta a consulta baseada nos campos disponíveis
                $fields = implode(", ", array_intersect($especialidadesFields, ['id', 'nome', 'status']));
                $whereClause = in_array('status', $especialidadesFields) ? " WHERE status = 1" : "";
                $stmtEsp = $pdo->query("SELECT {$fields} FROM especialidades{$whereClause} ORDER BY nome");
                $especialidades = $stmtEsp->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        // Consulta para clínicas (com verificação de erro)
        $clinicas = [];
        try {
            $stmtClin = $pdo->query("SELECT id, nome FROM clinicas_parceiras WHERE status = 1 ORDER BY nome");
            $clinicas = $stmtClin->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback se a consulta falhar - verificar campos
            if (in_array('clinicas_parceiras', $tables)) {
                // Adapta a consulta baseada nos campos disponíveis
                $fields = implode(", ", array_intersect($clinicasFields, ['id', 'nome', 'status']));
                $whereClause = in_array('status', $clinicasFields) ? " WHERE status = 1" : "";
                $stmtClin = $pdo->query("SELECT {$fields} FROM clinicas_parceiras{$whereClause} ORDER BY nome");
                $clinicas = $stmtClin->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        // Constrói o output
        $output = '
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="procedimento">Nome do Procedimento:</label>
                                    <input type="text" class="form-control" id="procedimento" name="procedimento" 
                                           value="'.htmlspecialchars($filtroProcedimento).'">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="especialidade_id">Especialidade:</label>
                                    <select class="form-control" id="especialidade_id" name="especialidade_id">
                                        <option value="">Todas as Especialidades</option>';
        
        // Adiciona opções de especialidades
        if (!empty($especialidades)) {
            foreach ($especialidades as $e) {
                if (isset($e['id']) && isset($e['nome'])) {
                    $selected = ($filtroEspecialidade == $e['id']) ? 'selected' : '';
                    $output .= '<option value="'.$e['id'].'" '.$selected.'>'.htmlspecialchars($e['nome']).'</option>';
                }
            }
        }
        
        $output .= '
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="clinica_id">Clínica:</label>
                                    <select class="form-control" id="clinica_id" name="clinica_id">
                                        <option value="">Todas as Clínicas</option>';
        
        // Adiciona opções de clínicas
        if (!empty($clinicas)) {
            foreach ($clinicas as $c) {
                if (isset($c['id']) && isset($c['nome'])) {
                    $selected = ($filtroClinica == $c['id']) ? 'selected' : '';
                    $output .= '<option value="'.$c['id'].'" '.$selected.'>'.htmlspecialchars($c['nome']).'</option>';
                }
            }
        }
        
        $output .= '
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" style="margin-top: 32px;">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Pesquisar
                                    </button>
                                </div>
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
                            <tbody>';
        
        // Adiciona linhas da tabela com os resultados
        if (count($resultados) > 0) {
            foreach ($resultados as $row) {
                $endereco = htmlspecialchars($row['endereco']);
                if (!empty($row['numero'])) $endereco .= ', ' . htmlspecialchars($row['numero']);
                if (!empty($row['bairro'])) $endereco .= ' - ' . htmlspecialchars($row['bairro']);
                $endereco .= '<br>' . htmlspecialchars($row['cidade']) . '/' . htmlspecialchars($row['estado']);
                
                $output .= '
                                <tr>
                                    <td>'.htmlspecialchars($row['procedimento']).'</td>
                                    <td>'.htmlspecialchars($row['especialidade']).'</td>
                                    <td class="text-right">R$ '.number_format($row['valor_procedimento'], 2, ',', '.').'</td>
                                    <td>'.htmlspecialchars($row['clinica']).'</td>
                                    <td>'.$endereco.'</td>
                                </tr>';
            }
        } else {
            $output .= '
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum procedimento encontrado com os critérios de pesquisa.</td>
                                </tr>';
        }
        
        $output .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        $(document).ready(function() {
            $("#dataTable").DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                pagingType: "full_numbers",
                ordering: true,
                searching: false,
                info: true,
                dom: "<\'top\'fl>rt<\'bottom\'ip><\'clear\'>"
            });
        });
        </script>';
        
        return $output;
        
    } catch (PDOException $e) {
        return '<div class="alert alert-danger">Erro de conexão: ' . $e->getMessage() . '</div>';
    }
}
?>

