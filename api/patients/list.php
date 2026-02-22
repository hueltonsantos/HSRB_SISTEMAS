<?php
/**
 * Endpoint de Listagem de Pacientes
 * GET /api/patients/list
 * 
 * Query params: ?page=1&limit=20&search=nome&status=1
 * Headers: Authorization: Bearer <token>
 * Response: { "success": true, "data": { "items": [...], "total": 150, "page": 1, "pages": 8 } }
 */

require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../../Database.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Método não permitido', 405);
}

// Verificar autenticação e permissão
$user = requireAuth();
requirePermission($user, 'appointment_view');

try {
    // Conectar ao banco
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Parâmetros de paginação
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    // Parâmetros de filtro
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? (int)$_GET['status'] : null;
    
    // Construir query
    $where = [];
    $params = [];
    
    // Filtro de busca
    if ($search) {
        $where[] = '(nome LIKE ? OR cpf LIKE ? OR celular LIKE ?)';
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Filtro de status
    if ($status !== null) {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Contar total de registros
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM pacientes $whereClause");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();
    
    // Buscar pacientes
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nome,
            cpf,
            data_nascimento,
            celular,
            email,
            endereco,
            cidade,
            estado,
            cep,
            status,
            data_cadastro
        FROM pacientes
        $whereClause
        ORDER BY nome ASC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar dados
    foreach ($pacientes as &$paciente) {
        $paciente['id'] = (int)$paciente['id'];
        $paciente['status'] = (int)$paciente['status'];
        
        // Formatar CPF (XXX.XXX.XXX-XX)
        if ($paciente['cpf']) {
            $cpf = preg_replace('/\D/', '', $paciente['cpf']);
            if (strlen($cpf) === 11) {
                $paciente['cpf_formatado'] = substr($cpf, 0, 3) . '.' . 
                                             substr($cpf, 3, 3) . '.' . 
                                             substr($cpf, 6, 3) . '-' . 
                                             substr($cpf, 9, 2);
            }
        }
        
        // Calcular idade
        if ($paciente['data_nascimento']) {
            $nascimento = new DateTime($paciente['data_nascimento']);
            $hoje = new DateTime();
            $paciente['idade'] = $hoje->diff($nascimento)->y;
        }
    }
    
    // Calcular total de páginas
    $pages = ceil($total / $limit);
    
    ApiResponse::success([
        'items' => $pacientes,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Erro ao listar pacientes: " . $e->getMessage());
    ApiResponse::serverError('Erro ao listar pacientes');
}
