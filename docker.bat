@echo off
REM Script de comandos Docker para Windows - Sistema de Clinicas

if "%1"=="" goto help
if "%1"=="up" goto up
if "%1"=="down" goto down
if "%1"=="build" goto build
if "%1"=="logs" goto logs
if "%1"=="shell" goto shell
if "%1"=="restart" goto restart
if "%1"=="clean" goto clean
if "%1"=="status" goto status
if "%1"=="install" goto install
goto help

:up
echo Iniciando containers...
docker-compose up -d
echo.
echo Sistema iniciado!
echo App: http://localhost:8080
echo phpMyAdmin: http://localhost:8081
goto end

:down
echo Parando containers...
docker-compose down
goto end

:build
echo Construindo imagens...
docker-compose build --no-cache
goto end

:logs
echo Mostrando logs (Ctrl+C para sair)...
docker-compose logs -f
goto end

:shell
echo Acessando shell do container...
docker-compose exec app bash
goto end

:restart
echo Reiniciando containers...
docker-compose restart
goto end

:clean
echo Limpando containers e volumes...
docker-compose down -v --remove-orphans
goto end

:status
docker-compose ps
goto end

:install
echo Instalando e iniciando o sistema...
docker-compose build
docker-compose up -d
echo.
echo ========================================
echo   Sistema instalado com sucesso!
echo ========================================
echo   App:        http://localhost:8080
echo   phpMyAdmin: http://localhost:8081
echo ========================================
goto end

:help
echo.
echo Comandos disponiveis:
echo   docker.bat install  - Primeira instalacao (build + up)
echo   docker.bat up       - Iniciar containers
echo   docker.bat down     - Parar containers
echo   docker.bat build    - Reconstruir imagens
echo   docker.bat logs     - Ver logs em tempo real
echo   docker.bat shell    - Acessar terminal do container
echo   docker.bat restart  - Reiniciar containers
echo   docker.bat clean    - Limpar tudo (containers + volumes)
echo   docker.bat status   - Ver status dos containers
echo.

:end
