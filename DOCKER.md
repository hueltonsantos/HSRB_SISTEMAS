# Docker - Sistema de Clínicas

Guia de comandos para rodar o sistema com Docker.

---

## Pré-requisitos

- Docker Desktop instalado
- Docker Compose

---

## Estrutura de Arquivos

```
├── Dockerfile              # Imagem PHP 8.2 + Apache
├── docker-compose.yml      # Desenvolvimento (com phpMyAdmin)
├── docker-compose.prod.yml # Produção (otimizado)
├── docker/
│   └── php.ini             # Configurações PHP
├── .env                    # Variáveis desenvolvimento
├── .env.prod               # Variáveis produção
├── docker.bat              # Script Windows
└── Makefile                # Script Linux/Mac
```

---

## Desenvolvimento

### Build da Imagem

```bash
docker build -t hsrb_sistemas:development .
```

### Iniciar Containers

```bash
# Usando docker-compose
docker-compose up -d

# Ou com build incluso
docker-compose up --build -d

# Windows (script)
docker.bat up

# Linux/Mac
make up
```

### Acessos (Desenvolvimento)

| Serviço    | URL                    |
|------------|------------------------|
| Aplicação  | http://localhost:8080  |
| phpMyAdmin | http://localhost:8081  |
| MySQL      | localhost:3307         |

### Parar Containers

```bash
docker-compose down

# Windows
docker.bat down
```

### Ver Logs

```bash
# Todos os containers
docker-compose logs -f

# Apenas a aplicação
docker-compose logs -f app

# Apenas o banco
docker-compose logs -f db
```

### Acessar Terminal do Container

```bash
# Shell da aplicação
docker-compose exec app bash

# MySQL via terminal
docker-compose exec db mysql -u clinica_user -pclinica_pass clinica_encaminhamento
```

### Rebuild (após mudanças no Dockerfile)

```bash
docker-compose build --no-cache
docker-compose up -d
```

---

## Produção

### Configurar Variáveis

Edite o arquivo `.env.prod` com senhas seguras:

```env
DB_NAME=clinica_encaminhamento
DB_USER=seu_usuario
DB_PASS=SENHA_SEGURA
MYSQL_ROOT_PASSWORD=SENHA_ROOT_SEGURA
BASE_URL=http://seu-dominio.com.br
```

### Iniciar em Produção

```bash
docker-compose -f docker-compose.prod.yml --env-file .env.prod up -d
```

### Acessos (Produção)

| Serviço    | URL                |
|------------|--------------------|
| Aplicação  | http://localhost   |
| MySQL      | Interno (sem porta exposta) |

### Parar Produção

```bash
docker-compose -f docker-compose.prod.yml down
```

### Ver Logs Produção

```bash
docker-compose -f docker-compose.prod.yml logs -f
```

---

## Comandos Úteis

### Status dos Containers

```bash
docker-compose ps
```

### Limpar Tudo (containers + volumes)

```bash
# CUIDADO: Apaga os dados do banco!
docker-compose down -v --remove-orphans
```

### Reiniciar Containers

```bash
docker-compose restart
```

### Ver Uso de Recursos

```bash
docker stats
```

### Backup do Banco de Dados

```bash
# Criar backup
docker-compose exec db mysqldump -u root -p123Mudar@ clinica_encaminhamento > backup.sql

# Restaurar backup
docker-compose exec -T db mysql -u root -p123Mudar@ clinica_encaminhamento < backup.sql
```

---

## Troubleshooting

### Erro de Conexão com Banco

1. Verifique se o container do banco está rodando:
   ```bash
   docker-compose ps
   ```

2. Aguarde o banco inicializar (primeira vez pode demorar):
   ```bash
   docker-compose logs -f db
   ```

### Permissão Negada na Pasta Uploads

```bash
docker-compose exec app chmod -R 777 /var/www/html/uploads
```

### Resetar Banco de Dados

```bash
# Para e remove volumes
docker-compose down -v

# Sobe novamente (recria o banco)
docker-compose up -d
```

### Ver IP do Container

```bash
docker inspect clinica_app | grep IPAddress
```

---

## Scripts Rápidos

### Windows (docker.bat)

```bash
docker.bat install   # Primeira instalação
docker.bat up        # Iniciar
docker.bat down      # Parar
docker.bat logs      # Ver logs
docker.bat shell     # Terminal
docker.bat restart   # Reiniciar
docker.bat clean     # Limpar tudo
docker.bat status    # Status
```

### Linux/Mac (Makefile)

```bash
make install   # Primeira instalação
make up        # Iniciar
make down      # Parar
make logs      # Ver logs
make shell     # Terminal
make restart   # Reiniciar
make clean     # Limpar tudo
make status    # Status
```

---

## Diferenças: Development vs Production

| Aspecto        | Development          | Production           |
|----------------|----------------------|----------------------|
| Porta App      | 8080                 | 80                   |
| phpMyAdmin     | Sim (8081)           | Não                  |
| Código         | Volume (hot reload)  | Copiado na imagem    |
| Erros PHP      | Exibidos             | Ocultados            |
| Restart        | unless-stopped       | always               |
| Logs           | Ilimitados           | Max 10MB             |
