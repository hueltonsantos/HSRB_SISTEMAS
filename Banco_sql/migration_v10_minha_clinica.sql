-- Criação da tabela de Convênios
CREATE TABLE IF NOT EXISTS master_convenios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_fantasia VARCHAR(255) NOT NULL,
    razao_social VARCHAR(255),
    cnpj VARCHAR(20),
    registro_ans VARCHAR(50),
    dias_retorno INT DEFAULT 30,
    prazo_recebimento_dias INT DEFAULT 30,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Tabela de Preços (Matriz Convênio x Procedimento)
CREATE TABLE IF NOT EXISTS master_tabela_precos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    convenio_id INT NOT NULL,
    procedimento_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    codigo_tuss VARCHAR(50),  -- Código específico para este convênio (ex: TUSS)
    codigo_interno VARCHAR(50), -- Caso o convênio use um código interno dele
    repasse_percentual DECIMAL(5,2) NULL, -- Override: Se preenchido, ignora o % do profissional
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (convenio_id) REFERENCES master_convenios(id),
    FOREIGN KEY (procedimento_id) REFERENCES master_procedimentos(id),
    UNIQUE KEY unique_preco (convenio_id, procedimento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Guias
CREATE TABLE IF NOT EXISTS master_guias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_guia VARCHAR(100) NOT NULL,
    paciente_id INT NOT NULL,
    convenio_id INT NOT NULL,
    profissional_id INT NOT NULL,
    agendamento_id INT UNIQUE, -- Vinculo 1 para 1 com agendamento
    tipo_guia ENUM('consulta', 'sadt', 'internacao', 'outros') DEFAULT 'consulta',
    status ENUM('solicitada', 'autorizada', 'negada', 'faturada', 'paga', 'glosada') DEFAULT 'solicitada',
    data_emissao DATE,
    data_autorizacao DATE,
    validade_senha DATE,
    motivo_glosa TEXT,
    valor_total DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
     FOREIGN KEY (convenio_id) REFERENCES master_convenios(id),
     FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
     FOREIGN KEY (profissional_id) REFERENCES master_profissionais(id),
     FOREIGN KEY (agendamento_id) REFERENCES master_agendamentos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: Como uma guia pode ter varios procedimentos, idealmente teriamos master_guias_itens. 
-- Simplificando para o MVP assumindo vinculo direto ao agendamento que ja tem procedimentos. 
-- Mas para faturamento correto de S/ADT complexo, precisaria de itens.
-- Vou manter simples agora vinculado ao agendamento.

-- Criação da tabela de Configuração de Profissionais (Vínculo Usuário x Profissional)
CREATE TABLE IF NOT EXISTS master_profissionais_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    usuario_sistema_id INT, -- ID da tabela de usuários do sistema de login (se houver)
    repasse_padrao_percentual DECIMAL(5,2) DEFAULT 0.00,
    ativo TINYINT(1) DEFAULT 1,
    data_inicio_vinculo DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profissional_id) REFERENCES master_profissionais(id),
    UNIQUE KEY unique_profissional (profissional_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Evoluções (Prontuário com Versionamento)
CREATE TABLE IF NOT EXISTS master_evolucoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    agendamento_id INT,
    texto LONGTEXT NOT NULL,
    cid10 VARCHAR(10),
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    versao INT DEFAULT 1,
    id_original INT NULL, -- Se for uma edição, aponta para o ID da primeira versão
    ativo TINYINT(1) DEFAULT 1, -- Apenas a última versão fica ativa
    assinatura_digital_hash VARCHAR(255), -- Hash para garantir integridade
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (profissional_id) REFERENCES master_profissionais(id),
    FOREIGN KEY (agendamento_id) REFERENCES master_agendamentos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela Financeiro - Caixa Previsto (Competência)
CREATE TABLE IF NOT EXISTS master_financeiro_caixa_previsto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    valor_previsto DECIMAL(10,2) NOT NULL,
    data_vencimento DATE NOT NULL,
    agendamento_id INT,
    guia_id INT,
    convenio_id INT,
    status ENUM('pendente', 'liquidado', 'cancelado') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES master_agendamentos(id),
    FOREIGN KEY (guia_id) REFERENCES master_guias(id),
    FOREIGN KEY (convenio_id) REFERENCES master_convenios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela Financeiro - Caixa Realizado (Caixa)
CREATE TABLE IF NOT EXISTS master_financeiro_caixa_realizado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    previsao_id INT, -- Referencia ao item previsto que originou este recebimento
    descricao VARCHAR(255) NOT NULL,
    valor_recebido DECIMAL(10,2) NOT NULL,
    data_recebimento DATE NOT NULL,
    forma_pagamento VARCHAR(50),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (previsao_id) REFERENCES master_financeiro_caixa_previsto(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Repasses
CREATE TABLE IF NOT EXISTS master_financeiro_repasses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    periodo_inicio DATE NOT NULL,
    periodo_fim DATE NOT NULL,
    valor_total_producao DECIMAL(10,2), -- Valor total dos procedimentos
    valor_glosas DECIMAL(10,2) DEFAULT 0, -- Valor descontado por glosas
    valor_taxas_clinica DECIMAL(10,2) DEFAULT 0, -- Parte da clínica
    valor_impostos DECIMAL(10,2) DEFAULT 0,
    valor_liquido_repasse DECIMAL(10,2), -- O que efetivamente vai para o médico
    status ENUM('calculado', 'pago', 'cancelado') DEFAULT 'calculado',
    data_pagamento DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profissional_id) REFERENCES master_profissionais(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item de Repasse (Detalhe de quais atendimentos geraram o repasse)
CREATE TABLE IF NOT EXISTS master_financeiro_repasses_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repasse_id INT NOT NULL,
    caixa_realizado_id INT NOT NULL, -- O repasse é sobre o dinheiro que ENTROU
    valor_base_item DECIMAL(10,2),
    percentual_aplicado DECIMAL(5,2),
    valor_comissao DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repasse_id) REFERENCES master_financeiro_repasses(id),
    FOREIGN KEY (caixa_realizado_id) REFERENCES master_financeiro_caixa_realizado(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTERAÇÕES EM TABELAS EXISTENTES
-- Como não podemos garantir que a coluna não existe, o ideal em SQL puro sem procedure é tentar adicionar.
-- Mas em script direto, se falhar, para tudo.
-- Vou assumir que estou rodando num ambiente controlado.

-- Adicionar colunas em master_agendamentos
-- ATENÇÃO: Se as colunas já existirem, isso pode dar erro. O PHP vai tratar ignorando erros específicos.

-- Tentar adicionar convenio_id
SET @dbname = DATABASE();
SET @tablename = "master_agendamentos";
SET @columnname = "convenio_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE master_agendamentos ADD COLUMN convenio_id INT NULL"
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tentar adicionar guia_id
SET @columnname = "guia_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE master_agendamentos ADD COLUMN guia_id INT NULL"
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar chaves estrangeiras se não existirem (simplificado, pode dar erro se já existir, mas o MySQL não suporta IF NOT EXISTS para FK diretamente de forma simples em um script corrido sem procedure complexa)
-- ALTER TABLE master_agendamentos ADD CONSTRAINT fk_agendamento_convenio FOREIGN KEY (convenio_id) REFERENCES master_convenios(id);

-- Adicionar colunas em master_procedimentos
SET @tablename = "master_procedimentos";
SET @columnname = "codigo_padrao";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE master_procedimentos ADD COLUMN codigo_padrao VARCHAR(50)"
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------
-- PERMISSÕES ADICIONAIS
-- -----------------------------------------------------

-- Adicionar novas permissões identificadas no código que faltavam
INSERT IGNORE INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Minha Clinica - Visualizar Geral', 'minha_clinica_ver', 'Permite visualizar convênios, tabelas de preço e guias'),
('Minha Clinica - Editar Geral', 'minha_clinica_editar', 'Permite criar/editar convênios, tabelas de preço e processar guias'),
('Minha Clinica - Configurações', 'minha_clinica_config', 'Acesso a configurações do módulo (ex: vínculo profissionais)'),
('Minha Clinica - Pacientes', 'minha_clinica_pacientes', 'Permite visualizar e gerenciar pacientes através do módulo Minha Clínica'),
('Minha Clinica - Gestão Financeira', 'minha_clinica_financeiro', 'Acesso completo ao financeiro, repasses e caixa da Minha Clínica');

-- Garantir que o admin (ID 1) tenha essas permissões
INSERT IGNORE INTO `perfil_permissoes` (`perfil_id`, `permissao_id`)
SELECT 1, id FROM `permissoes` WHERE `chave` IN (
    'minha_clinica_ver', 
    'minha_clinica_editar', 
    'minha_clinica_config', 
    'minha_clinica_pacientes',
    'minha_clinica_financeiro'
);
