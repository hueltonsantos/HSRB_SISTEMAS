-- Migration V9: Módulo de Caixa e Repasses
-- Data: 2026-02-15

-- Lançamentos de caixa (entradas e saídas)
CREATE TABLE IF NOT EXISTS caixa_lancamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    tipo ENUM('entrada','saida') NOT NULL,
    categoria VARCHAR(100) DEFAULT NULL,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    forma_pagamento ENUM('dinheiro','pix','cartao_credito','cartao_debito','convenio','transferencia') NOT NULL,
    agendamento_id INT DEFAULT NULL,
    paciente_id INT DEFAULT NULL,
    clinica_id INT DEFAULT NULL,
    usuario_id INT NOT NULL,
    fechamento_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_data (data),
    INDEX idx_tipo (tipo),
    INDEX idx_fechamento (fechamento_id),
    INDEX idx_agendamento (agendamento_id),
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (clinica_id) REFERENCES clinicas_parceiras(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fechamento de caixa diário
CREATE TABLE IF NOT EXISTS caixa_fechamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    saldo_inicial DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_entradas DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_saidas DECIMAL(10,2) NOT NULL DEFAULT 0,
    saldo_final DECIMAL(10,2) NOT NULL DEFAULT 0,
    observacoes TEXT DEFAULT NULL,
    status ENUM('aberto','fechado') DEFAULT 'aberto',
    usuario_abertura_id INT NOT NULL,
    usuario_fechamento_id INT DEFAULT NULL,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_fechamento DATETIME DEFAULT NULL,
    INDEX idx_data (data),
    INDEX idx_status (status),
    FOREIGN KEY (usuario_abertura_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_fechamento_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Adicionar FK de fechamento nos lançamentos
ALTER TABLE caixa_lancamentos
ADD FOREIGN KEY (fechamento_id) REFERENCES caixa_fechamentos(id);

-- Repasses para clínicas parceiras
CREATE TABLE IF NOT EXISTS repasses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinica_id INT NOT NULL,
    periodo_inicio DATE NOT NULL,
    periodo_fim DATE NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    valor_pago DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pendente','parcial','pago') DEFAULT 'pendente',
    data_pagamento DATE DEFAULT NULL,
    observacoes TEXT DEFAULT NULL,
    usuario_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clinica (clinica_id),
    INDEX idx_status (status),
    INDEX idx_periodo (periodo_inicio, periodo_fim),
    FOREIGN KEY (clinica_id) REFERENCES clinicas_parceiras(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Itens do repasse (detalhamento por procedimento)
CREATE TABLE IF NOT EXISTS repasse_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repasse_id INT NOT NULL,
    agendamento_id INT NOT NULL,
    procedimento_id INT NOT NULL,
    valor_procedimento DECIMAL(10,2) NOT NULL,
    valor_repasse DECIMAL(10,2) NOT NULL,
    INDEX idx_repasse (repasse_id),
    FOREIGN KEY (repasse_id) REFERENCES repasses(id) ON DELETE CASCADE,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissões do módulo
INSERT INTO permissoes (nome, chave, descricao) VALUES
('Visualizar Caixa', 'caixa_view', 'Permite visualizar lançamentos e fechamentos de caixa'),
('Gerenciar Caixa', 'caixa_manage', 'Permite criar lançamentos, abrir e fechar caixa'),
('Visualizar Repasses', 'repasse_view', 'Permite visualizar repasses de clínicas parceiras'),
('Gerenciar Repasses', 'repasse_manage', 'Permite gerar e registrar pagamentos de repasses');

-- Conceder permissões ao perfil Administrador (id=1)
INSERT INTO perfil_permissoes (perfil_id, permissao_id)
SELECT 1, id FROM permissoes WHERE chave IN ('caixa_view','caixa_manage','repasse_view','repasse_manage');
