# 🐳 Comandos Docker - VPS

## ✅ Solução Correta

Você **NÃO precisa rebuild**! O `docker-compose.yml` já tem volume mapeado:

```yaml
volumes:
  - .:/var/www/html # Isso mapeia TUDO automaticamente
```

## 🚀 Comandos Corretos

### No VPS (via SSH):

```bash
# 1. Parar containers
docker-compose down

# 2. Subir novamente (sem rebuild)
docker-compose up -d

# 3. Ver logs
docker-compose logs -f app
```

**Pronto!** A pasta `/api` já está lá! ✅

---

## 🔍 Verificar se funcionou

```bash
# Entrar no container
docker exec -it clinica_app bash

# Verificar se pasta existe
ls -la /var/www/html/api/

# Deve mostrar:
# auth/
# config/
# dashboard/
# patients/
# .htaccess
# README.md

# Sair
exit
```

---

## 🧪 Testar API

```bash
# Teste simples
curl http://localhost:8080/api/auth/login \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"hsrbsistemas@gmail.com","senha":"123Mudar@"}'
```

**Resposta esperada:**

```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "data": {
    "token": "eyJ...",
    "user": {...}
  }
}
```

---

## ⚠️ Se ainda não funcionar

### Verificar se Apache está com mod_headers habilitado:

```bash
docker exec -it clinica_app bash
a2enmod headers
a2enmod rewrite
service apache2 restart
exit
```

### Verificar permissões:

```bash
docker exec -it clinica_app bash
chown -R www-data:www-data /var/www/html/api
chmod -R 755 /var/www/html/api
exit
```

---

## 📱 Depois que funcionar

1. ✅ Testar API no VPS
2. ✅ Instalar APK no celular
3. ✅ Fazer login no app
4. ✅ Celebrar! 🎉
