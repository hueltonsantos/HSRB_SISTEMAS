-- =====================================================
-- MIGRACAO: Sistema Kanban
-- Data: 2026-02-07
-- =====================================================

-- Tabela de Quadros (Projetos)
CREATE TABLE IF NOT EXISTS `kanban_quadros` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `descricao` text DEFAULT NULL,
    `cor` varchar(7) DEFAULT '#4e73df',
    `criado_por` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT 1,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_criado_por` (`criado_por`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Colunas
CREATE TABLE IF NOT EXISTS `kanban_colunas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `quadro_id` int(11) NOT NULL,
    `nome` varchar(50) NOT NULL,
    `cor` varchar(7) DEFAULT '#858796',
    `ordem` int(11) DEFAULT 0,
    `limite_cards` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_quadro` (`quadro_id`),
    KEY `idx_ordem` (`ordem`),
    CONSTRAINT `fk_coluna_quadro` FOREIGN KEY (`quadro_id`) REFERENCES `kanban_quadros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Cards (Tarefas)
CREATE TABLE IF NOT EXISTS `kanban_cards` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `coluna_id` int(11) NOT NULL,
    `titulo` varchar(200) NOT NULL,
    `descricao` text DEFAULT NULL,
    `cor_etiqueta` varchar(7) DEFAULT NULL,
    `prioridade` enum('baixa','media','alta','urgente') DEFAULT 'media',
    `responsavel_id` int(11) DEFAULT NULL,
    `data_vencimento` date DEFAULT NULL,
    `ordem` int(11) DEFAULT 0,
    `criado_por` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT 1,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_coluna` (`coluna_id`),
    KEY `idx_responsavel` (`responsavel_id`),
    KEY `idx_prioridade` (`prioridade`),
    KEY `idx_ordem` (`ordem`),
    CONSTRAINT `fk_card_coluna` FOREIGN KEY (`coluna_id`) REFERENCES `kanban_colunas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Comentarios
CREATE TABLE IF NOT EXISTS `kanban_comentarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `card_id` int(11) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    `comentario` text NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_card` (`card_id`),
    CONSTRAINT `fk_comentario_card` FOREIGN KEY (`card_id`) REFERENCES `kanban_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Historico de Movimentacoes
CREATE TABLE IF NOT EXISTS `kanban_historico` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `card_id` int(11) NOT NULL,
    `usuario_id` int(11) DEFAULT NULL,
    `acao` varchar(50) NOT NULL,
    `coluna_origem_id` int(11) DEFAULT NULL,
    `coluna_destino_id` int(11) DEFAULT NULL,
    `detalhes` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_card` (`card_id`),
    KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Membros do Quadro (quem pode ver/editar)
CREATE TABLE IF NOT EXISTS `kanban_membros` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `quadro_id` int(11) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    `papel` enum('visualizador','editor','admin') DEFAULT 'editor',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_quadro_usuario` (`quadro_id`, `usuario_id`),
    CONSTRAINT `fk_membro_quadro` FOREIGN KEY (`quadro_id`) REFERENCES `kanban_quadros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir permissoes do Kanban
INSERT INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Visualizar Kanban', 'kanban_view', 'Permite visualizar quadros Kanban'),
('Gerenciar Kanban', 'kanban_manage', 'Permite criar/editar quadros, colunas e cards')
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);

-- Quadro de exemplo
INSERT INTO `kanban_quadros` (`nome`, `descricao`, `cor`) VALUES
('Tarefas da Equipe', 'Quadro principal para gerenciar tarefas do dia a dia', '#4e73df');

-- Colunas padrao para o quadro de exemplo
SET @quadro_id = LAST_INSERT_ID();

INSERT INTO `kanban_colunas` (`quadro_id`, `nome`, `cor`, `ordem`) VALUES
(@quadro_id, 'A Fazer', '#858796', 1),
(@quadro_id, 'Em Andamento', '#f6c23e', 2),
(@quadro_id, 'Em Revisao', '#36b9cc', 3),
(@quadro_id, 'Concluido', '#1cc88a', 4);
