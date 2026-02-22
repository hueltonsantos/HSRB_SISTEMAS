# 🚀 Guia de Deploy - VPS em Produção

## 📋 Checklist de Deploy

### 1. Backend API (PHP)

#### Configurações de Segurança

**`api/config/jwt_config.php`**

```php
// ⚠️ ALTERAR EM PRODUÇÃO
define('JWT_SECRET_KEY', 'SUA_CHAVE_SECRETA_SUPER_FORTE_AQUI_MIN_32_CHARS');
```

**`api/.htaccess`**

```apache
# ⚠️ ALTERAR CORS em produção
# Trocar de:
Header always set Access-Control-Allow-Origin "*"

# Para (substitua pelo domínio do seu app):
Header always set Access-Control-Allow-Origin "https://seudominio.com"
```

**`api/config/api_config.php`**

```php
// ⚠️ ALTERAR CORS em produção
// Trocar de:
header('Access-Control-Allow-Origin: *');

// Para:
$allowed_origins = [
    'https://seudominio.com',
    'https://www.seudominio.com',
    'https://app.seudominio.com'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
```

#### Configurações do Banco de Dados

**`config.php` ou `.env`**

```php
// Produção
define('DB_HOST', 'localhost'); // ou IP do servidor MySQL
define('DB_NAME', 'hsrb_sistemas_prod');
define('DB_USER', 'usuario_prod');
define('DB_PASS', 'senha_forte_aqui');
```

#### Habilitar HTTPS

```apache
# Forçar HTTPS no .htaccess raiz
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

### 2. App Mobile Flutter

#### Alterar URL da API

**`lib/core/constants/api_endpoints.dart`**

```dart
// ⚠️ ALTERAR PARA PRODUÇÃO
class ApiEndpoints {
  ApiEndpoints._();

  // DESENVOLVIMENTO (localhost)
  // static const String baseUrl = 'http://localhost:8080/api';

  // PRODUÇÃO (VPS)
  static const String baseUrl = 'https://seudominio.com/api';

  // Ou usar variável de ambiente
  static const String baseUrl = String.fromEnvironment(
    'API_URL',
    defaultValue: 'https://seudominio.com/api',
  );

  // ... resto do código
}
```

#### Build para Produção

**Android:**

```bash
# APK para testes
flutter build apk --release

# App Bundle para Google Play Store
flutter build appbundle --release --build-name=1.0.0 --build-number=1
```

**iOS:**

```bash
flutter build ios --release
```

**Web:**

```bash
flutter build web --release --web-renderer html
```

---

### 3. Configuração do VPS

#### Estrutura de Diretórios

```
/var/www/html/
├── api/                    # Backend PHP
│   ├── auth/
│   ├── config/
│   ├── dashboard/
│   ├── patients/
│   └── .htaccess
├── web/                    # Flutter Web (opcional)
│   └── (arquivos do build)
└── .htaccess              # Raiz
```

#### Apache Virtual Host

```apache
<VirtualHost *:80>
    ServerName seudominio.com
    ServerAlias www.seudominio.com

    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/hsrb_error.log
    CustomLog ${APACHE_LOG_DIR}/hsrb_access.log combined
</VirtualHost>
```

#### SSL com Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obter certificado SSL
sudo certbot --apache -d seudominio.com -d www.seudominio.com

# Renovação automática (já configurado pelo certbot)
sudo certbot renew --dry-run
```

---

### 4. Variáveis de Ambiente (Recomendado)

#### Criar arquivo `.env` na raiz

```env
# Banco de Dados
DB_HOST=localhost
DB_NAME=hsrb_sistemas_prod
DB_USER=usuario_prod
DB_PASS=senha_forte_aqui

# JWT
JWT_SECRET_KEY=sua_chave_secreta_super_forte_min_32_chars

# API
API_URL=https://seudominio.com/api
BASE_URL=https://seudominio.com

# Ambiente
APP_ENV=production
APP_DEBUG=false
```

#### Carregar variáveis no PHP

```php
// config.php
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');

    define('DB_HOST', $env['DB_HOST']);
    define('DB_NAME', $env['DB_NAME']);
    define('DB_USER', $env['DB_USER']);
    define('DB_PASS', $env['DB_PASS']);
    define('JWT_SECRET_KEY', $env['JWT_SECRET_KEY']);
}
```

#### Flutter com variáveis de ambiente

```bash
# Build com variável de ambiente
flutter build apk --release --dart-define=API_URL=https://seudominio.com/api
```

---

### 5. Segurança Adicional

#### Proteger arquivo `.env`

```apache
# .htaccess na raiz
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

#### Rate Limiting (Nginx)

```nginx
limit_req_zone $binary_remote_addr zone=api_limit:10m rate=10r/s;

location /api/ {
    limit_req zone=api_limit burst=20 nodelay;
}
```

#### Firewall (UFW)

```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

---

### 6. Monitoramento

#### Logs da API

```php
// Configurar logs em produção
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/hsrb_api_errors.log');
```

#### Backup do Banco

```bash
# Criar script de backup diário
#!/bin/bash
mysqldump -u usuario_prod -p'senha' hsrb_sistemas_prod > /backups/hsrb_$(date +%Y%m%d).sql
```

---

## 🔄 Fluxo de Deploy

### Primeira vez:

1. ✅ Configurar VPS (Apache, PHP, MySQL)
2. ✅ Fazer upload dos arquivos via FTP/Git
3. ✅ Importar banco de dados
4. ✅ Configurar `.env` com dados de produção
5. ✅ Alterar `JWT_SECRET_KEY`
6. ✅ Configurar CORS para domínio específico
7. ✅ Instalar certificado SSL
8. ✅ Testar API: `https://seudominio.com/api/auth/login`
9. ✅ Fazer build do app Flutter com URL de produção
10. ✅ Publicar app nas lojas (Google Play / App Store)

### Atualizações:

1. ✅ Fazer alterações no código
2. ✅ Testar localmente
3. ✅ Fazer upload via Git/FTP
4. ✅ Rebuild do app Flutter (se necessário)
5. ✅ Publicar nova versão nas lojas

---

## 📱 Publicação nas Lojas

### Google Play Store

1. Criar conta de desenvolvedor ($25 única vez)
2. Gerar keystore para assinatura:

```bash
keytool -genkey -v -keystore ~/upload-keystore.jks -keyalg RSA -keysize 2048 -validity 10000 -alias upload
```

3. Configurar `android/key.properties`:

```properties
storePassword=sua_senha
keyPassword=sua_senha
keyAlias=upload
storeFile=/caminho/para/upload-keystore.jks
```

4. Build e upload:

```bash
flutter build appbundle --release
```

### Apple App Store

1. Conta de desenvolvedor Apple ($99/ano)
2. Configurar certificados no Xcode
3. Build:

```bash
flutter build ios --release
```

4. Upload via Xcode ou Transporter

---

## 🎯 Resumo Rápido

**Para VPS:**

1. Alterar `baseUrl` em `api_endpoints.dart` para `https://seudominio.com/api`
2. Alterar `JWT_SECRET_KEY` em `jwt_config.php`
3. Configurar CORS para domínio específico (não usar `*`)
4. Habilitar HTTPS com Let's Encrypt
5. Configurar variáveis de ambiente (`.env`)

**Para App:**

1. Build com URL de produção
2. Testar conexão com API
3. Publicar nas lojas

---

## 📞 Suporte

Se tiver dúvidas durante o deploy, me avise! 🚀
