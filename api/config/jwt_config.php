<?php
/**
 * Configuração JWT para API Mobile
 * Gerenciamento de tokens de autenticação
 */

// Chave secreta para assinar tokens (ALTERE EM PRODUÇÃO!)
define('JWT_SECRET_KEY', 'hsrb_sistemas_secret_key_2026_change_in_production');

// Algoritmo de criptografia
define('JWT_ALGORITHM', 'HS256');

// Tempo de expiração do token (em segundos)
define('JWT_EXPIRATION_TIME', 86400); // 24 horas

// Tempo de expiração do refresh token (em segundos)
define('JWT_REFRESH_EXPIRATION_TIME', 604800); // 7 dias

// Issuer (emissor do token)
define('JWT_ISSUER', 'HSRB_SISTEMAS');

// Audience (público-alvo do token)
define('JWT_AUDIENCE', 'HSRB_MOBILE_APP');

/**
 * Classe para gerenciar JWT tokens
 */
class JWT {
    
    /**
     * Gera um token JWT
     * @param array $payload Dados a serem incluídos no token
     * @return string Token JWT
     */
    public static function encode($payload) {
        $header = [
            'typ' => 'JWT',
            'alg' => JWT_ALGORITHM
        ];
        
        $payload['iat'] = time(); // Issued at
        $payload['exp'] = time() + JWT_EXPIRATION_TIME; // Expiration
        $payload['iss'] = JWT_ISSUER; // Issuer
        $payload['aud'] = JWT_AUDIENCE; // Audience
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET_KEY, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Decodifica e valida um token JWT
     * @param string $token Token JWT
     * @return array|false Payload do token ou false se inválido
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verificar assinatura
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET_KEY, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        // Decodificar payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // Verificar expiração
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        // Verificar issuer e audience
        if (isset($payload['iss']) && $payload['iss'] !== JWT_ISSUER) {
            return false;
        }
        
        if (isset($payload['aud']) && $payload['aud'] !== JWT_AUDIENCE) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Gera um refresh token
     * @param int $userId ID do usuário
     * @return string Refresh token
     */
    public static function generateRefreshToken($userId) {
        $payload = [
            'user_id' => $userId,
            'type' => 'refresh',
            'exp' => time() + JWT_REFRESH_EXPIRATION_TIME
        ];
        
        return self::encode($payload);
    }
    
    /**
     * Codifica em Base64 URL-safe
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodifica de Base64 URL-safe
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
