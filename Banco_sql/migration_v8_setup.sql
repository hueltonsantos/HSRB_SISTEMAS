-- Migration V8: Suporte ao Setup Wizard
-- Adiciona campos telefone e cpf na tabela usuarios para o cadastro inicial do administrador

ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `telefone` VARCHAR(20) DEFAULT NULL AFTER `email`;
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `cpf` VARCHAR(14) DEFAULT NULL AFTER `telefone`;
