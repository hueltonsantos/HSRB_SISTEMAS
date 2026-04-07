# HSRB Sistemas - Sistema de Gestão Clínica

Sistema web para gestão de clínicas médicas. Permite o gerenciamento de pacientes, agendamentos, prontuários eletrônicos, guias de encaminhamento, financeiro e muito mais.

---

## Tecnologias

- **Backend:** PHP (MVC sem framework)
- **Banco de dados:** MySQL / MariaDB
- **Frontend:** Bootstrap 4.6, jQuery, DataTables, Select2, Chart.js
- **API:** REST com autenticação JWT
- **Infraestrutura:** Docker (opcional)

---

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior (ou MariaDB 10.3+)
- Servidor web: Apache (XAMPP/WAMP) ou Nginx
- Extensões PHP: `pdo_mysql`, `mbstring`, `json`

---

## Instalacao

### Com XAMPP (local)

1. Clone o repositorio dentro de `htdocs`:
   ```bash
   git clone https://github.com/hueltonsantos/HSRB_SISTEMAS.git
   ```

2. Copie o arquivo de configuracao de ambiente:
   ```bash
   cp .env.example .env
   ```

3. Edite o `.env` com as credenciais do seu banco de dados.

4. Crie o banco de dados e importe as migrations em ordem:
   ```
   Banco_sql/clinica_encaminhamento.sql
   Banco_sql/migration_v2.sql
   Banco_sql/migration_v3_foto.sql
   ...
   Banco_sql/migration_v16_prontuario_permissao.sql
   ```

5. Acesse pelo navegador:
   ```
   http://localhost/HSRB_SISTEMAS
   ```

### Com Docker

1. Clone o repositorio e configure o `.env`:
   ```bash
   git clone https://github.com/hueltonsantos/HSRB_SISTEMAS.git
   cp .env.example .env
   ```

2. Suba os containers:
   ```bash
   docker-compose up -d
   ```

3. Acesse em `http://localhost:8080`

---

## Configuracao

O sistema usa variaveis de ambiente para configuracao. Crie um arquivo `.env` na raiz com base no `.env.example`:

```env
DB_HOST=localhost
DB_NAME=clinica_encaminhamento
DB_USER=root
DB_PASS=

APP_ENV=development
APP_DEBUG=true
```

O arquivo `.env` nunca deve ser versionado (ja esta no `.gitignore`).

---

## Modulos

| Modulo | Descricao |
|---|---|
| Pacientes | Cadastro completo de pacientes com historico |
| Agendamentos | Agenda e calendario de consultas |
| Prontuario Eletronico | Registro de evolucoes clinicas com assinatura digital |
| Guias de Encaminhamento | Emissao e controle de guias |
| Minha Clinica | Configuracoes da clinica, profissionais, convenios e procedimentos |
| Financeiro | Repasses, inadimplencia e dashboard financeiro |
| Kanban | Fluxo de atendimento visual |
| Dashboard | Indicadores e graficos em tempo real |
| Relatorios | Exportacao de dados operacionais e financeiros |
| Usuarios e Perfis | Controle de acesso por perfil com permissoes granulares |

---

## Permissoes

O sistema possui controle de acesso por perfis. As permissoes sao gerenciadas em:
**Painel Admin > Perfis > Editar perfil**

Principais permissoes:

| Chave | Descricao |
|---|---|
| `ver_prontuario` | Visualizar e imprimir prontuario completo |
| `painel_profissional` | Acesso ao painel do medico/profissional |
| `minha_clinica_pacientes` | Visualizar pacientes da clinica |
| `imprimir_evolucao` | Imprimir evolucoes clinicas individuais |
| `appointment_view` | Visualizar agendamentos |
| `appointment_create` | Criar agendamentos |
| `appointment_edit` | Editar agendamentos |

---

## Estrutura do Projeto

```
HSRB_SISTEMAS/
├── api/                  # Endpoints REST (JWT)
├── modulos/              # Modulos da interface web
│   ├── pacientes/
│   ├── agendamentos/
│   ├── minha_clinica/
│   ├── relatorios/
│   └── ...
├── Banco_sql/            # Migrations do banco de dados
├── assents/              # CSS, JS e imagens
├── uploads/              # Arquivos enviados pelos usuarios
├── config.php            # Configuracoes globais
├── index.php             # Roteador principal
├── auth.php              # Autenticacao e permissoes
└── docker-compose.yml    # Configuracao Docker
```

---

## Migrations

Execute as migrations em ordem numerica sempre que atualizar o sistema:

```bash
# No MySQL / phpMyAdmin, execute os arquivos em Banco_sql/ na ordem:
# clinica_encaminhamento.sql -> migration_v2.sql -> ... -> migration_v16_prontuario_permissao.sql
```

---

## Acesso Inicial

Apos instalar, acesse o sistema com o usuario administrador configurado durante o setup (`setup.php`).

O perfil **Administrador** recebe automaticamente todas as permissoes basicas via migrations.

---

## Licenca

Projeto proprietario. Todos os direitos reservados.

Desenvolvido por **Huelton Santos**.

---

## Contato

- LinkedIn: https://www.linkedin.com/in/huelton-santosdvs/
- Docker Hub: https://hub.docker.com/r/notleuh/hsrb_sistemas_clinicas
- WhatsApp: (77) 99988-2930
- E-mail: hueltonti@gmail.com
