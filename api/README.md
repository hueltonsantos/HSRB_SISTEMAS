# API REST - HSRB_SISTEMAS Mobile

API REST para integração com o aplicativo mobile Flutter do sistema de atendimento clínico.

## 📋 Visão Geral

Esta API fornece endpoints para autenticação e gerenciamento de dados do sistema clínico, utilizando JWT (JSON Web Tokens) para autenticação segura.

## 🔐 Autenticação

Todos os endpoints (exceto `/auth/login`) requerem um token JWT válido no header `Authorization`:

```
Authorization: Bearer <seu_token_jwt>
```

### Endpoints de Autenticação

#### POST `/api/auth/login`

Realiza login e retorna token JWT.

**Request:**

```json
{
  "email": "user@example.com",
  "senha": "password"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "refresh_token": "eyJhbGciOiJIUzI1NiIs...",
    "user": {
      "id": 1,
      "nome": "João Silva",
      "email": "user@example.com",
      "perfil_id": 2,
      "perfil_nome": "Recepcionista",
      "clinica_id": 1,
      "foto": "uploads/usuarios/foto.jpg",
      "permissoes": ["appointment_view", "appointment_create"]
    }
  }
}
```

#### POST `/api/auth/refresh`

Renova um token expirado usando o refresh token.

**Request:**

```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIs..."
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Token renovado com sucesso",
  "data": {
    "token": "novo_token_jwt",
    "refresh_token": "novo_refresh_token"
  }
}
```

#### POST `/api/auth/logout`

Realiza logout (registra log).

**Headers:**

```
Authorization: Bearer <token>
```

**Response (200):**

```json
{
  "success": true,
  "message": "Logout realizado com sucesso"
}
```

#### GET `/api/auth/me`

Retorna dados do usuário autenticado.

**Headers:**

```
Authorization: Bearer <token>
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "nome": "João Silva",
      "email": "user@example.com",
      "perfil_id": 2,
      "perfil_nome": "Recepcionista",
      "clinica_id": 1,
      "foto": "uploads/usuarios/foto.jpg",
      "telefone": "(77) 99988-2930",
      "ultimo_acesso": "2026-02-12 12:45:00",
      "permissoes": ["appointment_view", "appointment_create"]
    }
  }
}
```

## 📊 Estrutura de Resposta

### Sucesso

```json
{
  "success": true,
  "message": "Mensagem de sucesso",
  "data": { ... },
  "timestamp": "2026-02-12 12:45:00"
}
```

### Erro

```json
{
  "success": false,
  "message": "Mensagem de erro",
  "errors": { ... },
  "timestamp": "2026-02-12 12:45:00"
}
```

## 🔒 Códigos de Status HTTP

- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Requisição inválida
- `401` - Não autorizado (token inválido/expirado)
- `403` - Proibido (sem permissão)
- `404` - Não encontrado
- `405` - Método não permitido
- `500` - Erro interno do servidor

## 🛡️ Segurança

- **JWT Tokens**: Expiração de 24 horas
- **Refresh Tokens**: Expiração de 7 dias
- **CORS**: Configurado para permitir requisições do app mobile
- **HTTPS**: Recomendado em produção
- **Rate Limiting**: A ser implementado

## 🔧 Configuração

### Alterar Chave Secreta JWT

Edite o arquivo `/api/config/jwt_config.php`:

```php
define('JWT_SECRET_KEY', 'sua_chave_secreta_aqui');
```

⚠️ **IMPORTANTE**: Altere a chave secreta em produção!

### Tempo de Expiração

```php
define('JWT_EXPIRATION_TIME', 86400); // 24 horas
define('JWT_REFRESH_EXPIRATION_TIME', 604800); // 7 dias
```

## 📝 Próximos Endpoints

- `/api/dashboard/stats` - Estatísticas do dashboard
- `/api/patients/*` - CRUD de pacientes
- `/api/clinics/*` - CRUD de clínicas
- `/api/specialties/*` - CRUD de especialidades
- `/api/appointments/*` - CRUD de agendamentos
- `/api/guides/*` - CRUD de guias
- `/api/users/*` - CRUD de usuários (admin)

## 🧪 Testando a API

### Usando cURL

```bash
# Login
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"hsrbsistemas@gmail.com","senha":"123Mudar@"}'

# Obter dados do usuário
curl -X GET http://localhost:8080/api/auth/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### Usando Postman

1. Importe a collection (a ser criada)
2. Configure a variável `base_url` para `http://localhost:8080/api`
3. Execute os requests

## 📚 Documentação Completa

Para documentação completa de todos os endpoints, consulte o arquivo `API_DOCUMENTATION.md` (a ser criado).
