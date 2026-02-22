<?php
/**
 * Configuração da API
 * Helpers e utilitários para a API REST
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/jwt_config.php';

// ===== CORS =====
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Classe utilitária para respostas da API
 */
class ApiResponse {
    
    /**
     * Retorna resposta de sucesso
     * @param mixed $data Dados a retornar
     * @param string $message Mensagem opcional
     * @param int $statusCode Código HTTP
     */
    public static function success($data = null, $message = 'Success', $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Retorna resposta de erro
     * @param string $message Mensagem de erro
     * @param int $statusCode Código HTTP
     * @param array $errors Erros detalhados (opcional)
     */
    public static function error($message = 'Error', $statusCode = 400, $errors = null) {
        http_response_code($statusCode);
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Retorna resposta de não autorizado
     */
    public static function unauthorized($message = 'Não autorizado') {
        self::error($message, 401);
    }
    
    /**
     * Retorna resposta de proibido
     */
    public static function forbidden($message = 'Acesso negado') {
        self::error($message, 403);
    }
    
    /**
     * Retorna resposta de não encontrado
     */
    public static function notFound($message = 'Recurso não encontrado') {
        self::error($message, 404);
    }
    
    /**
     * Retorna resposta de erro interno
     */
    public static function serverError($message = 'Erro interno do servidor') {
        self::error($message, 500);
    }
}

/**
 * Classe para validação de requisições
 */
class ApiValidator {
    
    /**
     * Valida campos obrigatórios
     * @param array $data Dados a validar
     * @param array $required Campos obrigatórios
     * @return array|null Erros ou null se válido
     */
    public static function validateRequired($data, $required) {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "O campo $field é obrigatório";
            }
        }
        
        return empty($errors) ? null : $errors;
    }
    
    /**
     * Valida email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitiza string
     */
    public static function sanitizeString($string) {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Obtém dados do corpo da requisição (JSON)
 */
function getRequestData() {
    $data = json_decode(file_get_contents('php://input'), true);
    return $data ?? [];
}

/**
 * Alias para obter dados do JSON (compatibilidade)
 */
function getJsonInput() {
    return getRequestData();
}

/**
 * Obtém token do header Authorization
 */
function getBearerToken() {
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    
    return null;
}

/**
 * Middleware de autenticação
 * Verifica se o token JWT é válido
 * @return array Dados do usuário decodificados do token
 */
function requireAuth() {
    $token = getBearerToken();
    
    if (!$token) {
        ApiResponse::unauthorized('Token não fornecido');
    }
    
    $payload = JWT::decode($token);
    
    if (!$payload) {
        ApiResponse::unauthorized('Token inválido ou expirado');
    }
    
    return $payload;
}

/**
 * Verifica se o usuário tem permissão específica
 * @param array $user Dados do usuário (do token)
 * @param string $permission Permissão necessária
 */
function requirePermission($user, $permission) {
    // Super admin sempre tem acesso
    if (isset($user['email']) && $user['email'] === 'hueltonti@gmail.com') {
        return true;
    }
    
    if (!isset($user['permissoes']) || !is_array($user['permissoes'])) {
        ApiResponse::forbidden('Permissões não encontradas');
    }
    
    if (!in_array($permission, $user['permissoes'])) {
        ApiResponse::forbidden('Você não tem permissão para acessar este recurso');
    }
    
    return true;
}
