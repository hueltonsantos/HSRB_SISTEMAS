-- Migration V3 - Adiciona coluna foto na tabela usuarios

-- Adiciona coluna foto se não existir
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `foto` VARCHAR(255) DEFAULT NULL AFTER `email`;
