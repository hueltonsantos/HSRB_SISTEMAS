# 🐳 Build e Deploy - API Separada

## 📦 Arquitetura

Agora temos **3 serviços separados**:

1. **app** - Aplicação web principal (PHP)
2. **api** - API REST para mobile (PHP)
3. **db** - Banco de dados (MariaDB)

---

## 🚀 Passo a Passo

### 1. Build da Imagem da API

```bash
# No seu PC
cd c:\xampp\htdocs\clinica_2026\HSRB_SISTEMAS

# Build da imagem da API
docker build -f Dockerfile.api -t notleuh/hsrb_sistemas_clinicas:api-latest .
```

### 2. Testar Localmente (Opcional)

```bash
# Rodar container de teste
docker run -d -p 8082:80 --name test_api notleuh/hsrb_sistemas_clinicas:api-latest

# Testar
curl http://localhost:8082/api/auth/login \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"hsrbsistemas@gmail.com","senha":"123Mudar@"}'

# Parar e remover
docker stop test_api
docker rm test_api
```

### 3. Push para Docker Hub

```bash
# Login (se necessário)
docker login

# Push
docker push notleuh/hsrb_sistemas_clinicas:api-latest
```

### 4. Deploy no VPS

```bash
# SSH no VPS
ssh usuario@clinicas.hueltonsites.com.br

# Ir para pasta da stack
cd /caminho/da/stack

# Copiar novo docker-compose
# (você precisa fazer upload do docker-compose.swarm.yml)

# Remover stack antiga
docker stack rm clinica

# Deploy nova stack com API separada
docker stack deploy -c docker-compose.swarm.yml clinica

# Ver status
docker service ls
docker service logs clinica_api -f
```

---

## ✅ Verificar se Funcionou

### 1. Ver serviços rodando

```bash
docker service ls

# Deve mostrar:
# clinica_app
# clinica_api
# clinica_db
```

### 2. Testar API

```bash
curl https://clinicas.hueltonsites.com.br/api/auth/login \
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

## 📱 Depois que Funcionar

1. ✅ Instalar APK no celular
2. ✅ Fazer login no app
3. ✅ Testar funcionalidades
4. ✅ Celebrar! 🎉

---

## 🔧 Troubleshooting

### API não responde

```bash
# Ver logs
docker service logs clinica_api -f

# Ver detalhes do serviço
docker service ps clinica_api

# Inspecionar
docker service inspect clinica_api
```

### Erro 500

```bash
# Entrar no container
docker exec -it $(docker ps -q -f name=clinica_api) bash

# Ver logs do Apache
tail -f /var/log/apache2/error.log

# Verificar arquivos
ls -la /var/www/html/api/
```

---

## ⚡ Comandos Resumidos

```bash
# No PC
docker build -f Dockerfile.api -t notleuh/hsrb_sistemas_clinicas:api-latest .
docker push notleuh/hsrb_sistemas_clinicas:api-latest

# No VPS
docker stack rm clinica
docker stack deploy -c docker-compose.swarm.yml clinica
docker service logs clinica_api -f
```

Pronto! 🚀
