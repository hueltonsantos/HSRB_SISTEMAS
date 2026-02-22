ALTER TABLE `especialidades` ADD COLUMN IF NOT EXISTS `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp();
ALTER TABLE `especialidades` ADD COLUMN IF NOT EXISTS `ultima_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
