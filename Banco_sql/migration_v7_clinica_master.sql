-- Migration V7: Estrutura para Clinica Master
-- Data: 2026-02-09

-- Adicionar coluna tipo na tabela de clinicas
ALTER TABLE `clinicas_parceiras`
ADD COLUMN IF NOT EXISTS `tipo` ENUM('master', 'parceira') NOT NULL DEFAULT 'parceira' AFTER `status`;

-- Adicionar coluna percentual_repasse para flexibilidade
ALTER TABLE `clinicas_parceiras`
ADD COLUMN IF NOT EXISTS `percentual_repasse` DECIMAL(5,2) DEFAULT 50.00 AFTER `tipo`;

-- Criar tabela de especialidades da clinica master (separada das parceiras)
CREATE TABLE IF NOT EXISTS `master_especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de procedimentos da clinica master
CREATE TABLE IF NOT EXISTS `master_procedimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `especialidade_id` int(11) NOT NULL,
  `procedimento` varchar(200) NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duracao_minutos` int(11) DEFAULT 30,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_especialidade` (`especialidade_id`),
  CONSTRAINT `fk_master_proc_esp` FOREIGN KEY (`especialidade_id`) REFERENCES `master_especialidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de agendamentos da clinica master
CREATE TABLE IF NOT EXISTS `master_agendamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `procedimento_id` int(11) DEFAULT NULL,
  `profissional_id` int(11) DEFAULT NULL,
  `data_consulta` date NOT NULL,
  `hora_consulta` time NOT NULL,
  `status` enum('agendado','confirmado','em_atendimento','realizado','cancelado','faltou') DEFAULT 'agendado',
  `valor` decimal(10,2) DEFAULT 0.00,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_especialidade` (`especialidade_id`),
  KEY `idx_data` (`data_consulta`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_master_ag_pac` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_master_ag_esp` FOREIGN KEY (`especialidade_id`) REFERENCES `master_especialidades` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de relacionamento agendamento x procedimentos (multiplos procedimentos)
CREATE TABLE IF NOT EXISTS `master_agendamento_procedimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agendamento_id` int(11) NOT NULL,
  `procedimento_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_agendamento` (`agendamento_id`),
  KEY `idx_procedimento` (`procedimento_id`),
  CONSTRAINT `fk_master_ap_ag` FOREIGN KEY (`agendamento_id`) REFERENCES `master_agendamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_master_ap_proc` FOREIGN KEY (`procedimento_id`) REFERENCES `master_procedimentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de profissionais da clinica master
CREATE TABLE IF NOT EXISTS `master_profissionais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `especialidade_id` int(11) DEFAULT NULL,
  `registro_profissional` varchar(50) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_especialidade` (`especialidade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar novas permissoes para clinica master
INSERT IGNORE INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Minha Clinica - Dashboard', 'master_dashboard', 'Acesso ao dashboard da clinica master'),
('Minha Clinica - Agendamentos', 'master_agendamentos', 'Gerenciar agendamentos da clinica master'),
('Minha Clinica - Especialidades', 'master_especialidades', 'Gerenciar especialidades da clinica master'),
('Minha Clinica - Procedimentos', 'master_procedimentos', 'Gerenciar procedimentos da clinica master'),
('Minha Clinica - Profissionais', 'master_profissionais', 'Gerenciar profissionais da clinica master'),
('Minha Clinica - Financeiro', 'master_financeiro', 'Acesso ao financeiro da clinica master');

-- Dar permissoes ao perfil admin (id=1)
INSERT IGNORE INTO `perfil_permissoes` (`perfil_id`, `permissao_id`)
SELECT 1, id FROM `permissoes` WHERE `chave` LIKE 'master_%';
