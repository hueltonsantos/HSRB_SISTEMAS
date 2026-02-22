# Makefile para comandos Docker do Sistema de Clínicas

.PHONY: up down build logs shell db-shell clean restart

# Iniciar todos os containers
up:
	docker-compose up -d

# Parar todos os containers
down:
	docker-compose down

# Construir/reconstruir as imagens
build:
	docker-compose build --no-cache

# Ver logs em tempo real
logs:
	docker-compose logs -f

# Logs apenas da aplicação
logs-app:
	docker-compose logs -f app

# Acessar shell do container da aplicação
shell:
	docker-compose exec app bash

# Acessar MySQL via linha de comando
db-shell:
	docker-compose exec db mysql -u clinica_user -pclinica_pass clinica_encaminhamento

# Limpar volumes e containers
clean:
	docker-compose down -v --remove-orphans

# Reiniciar containers
restart:
	docker-compose restart

# Status dos containers
status:
	docker-compose ps

# Primeira execução (build + up)
install:
	docker-compose build
	docker-compose up -d
	@echo ""
	@echo "Sistema iniciado com sucesso!"
	@echo "Acesse: http://localhost:8080"
	@echo "phpMyAdmin: http://localhost:8081"
