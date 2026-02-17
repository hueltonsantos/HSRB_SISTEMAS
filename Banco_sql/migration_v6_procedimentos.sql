-- Migration V6: Tabela agendamento_procedimentos e campos auxiliares
-- Data: 2026-02-08

-- Criar tabela agendamento_procedimentos se não existir
CREATE TABLE IF NOT EXISTS `agendamento_procedimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agendamento_id` int(11) NOT NULL,
  `procedimento_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_agendamento` (`agendamento_id`),
  KEY `idx_procedimento` (`procedimento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar colunas valor_total e forma_pagamento na tabela agendamentos se não existirem
-- (Ignorar erro se já existirem)
ALTER TABLE `agendamentos` ADD COLUMN IF NOT EXISTS `valor_total` DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE `agendamentos` ADD COLUMN IF NOT EXISTS `forma_pagamento` VARCHAR(50) DEFAULT NULL;
