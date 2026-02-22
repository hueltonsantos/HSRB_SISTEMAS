-- Migration V2 - RBAC, Pricing, and Hierarchy

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- --------------------------------------------------------
-- 1. New Tables for RBAC (Roles & Permissions)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `perfis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `chave` varchar(50) NOT NULL UNIQUE,  -- System key e.g., 'user_create', 'report_view'
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `perfil_permissoes` (
  `perfil_id` int(11) NOT NULL,
  `permissao_id` int(11) NOT NULL,
  PRIMARY KEY (`perfil_id`, `permissao_id`),
  CONSTRAINT `fk_pp_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pp_permissao` FOREIGN KEY (`permissao_id`) REFERENCES `permissoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Seed Initial Roles and Permissions
-- --------------------------------------------------------

INSERT INTO `perfis` (`id`, `nome`, `descricao`) VALUES
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Médico', 'Acesso a agendas e prontuários'),
(3, 'Recepcionista', 'Acesso a agendamentos e cadastros básicos');

-- Insert Basic Permissions
INSERT INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Gerenciar Usuários', 'user_manage', 'Criar, editar e remover usuários'),
('Gerenciar Perfis', 'role_manage', 'Criar e editar perfis de acesso'),
('Visualizar Agendamentos', 'appointment_view', 'Ver agenda'),
('Criar Agendamento', 'appointment_create', 'Marcar consultas'),
('Gerenciar Preços', 'price_manage', 'Editar valores de procedimentos'),
('Visualizar Relatórios', 'report_view', 'Acessar área de relatórios');

-- Grant Admin all permissions
INSERT INTO `perfil_permissoes` (`perfil_id`, `permissao_id`)
SELECT 1, id FROM `permissoes`;

-- --------------------------------------------------------
-- 3. Modify Users Table (Hierarchy & Profile)
-- --------------------------------------------------------

-- Add new columns
ALTER TABLE `usuarios`
  ADD COLUMN `perfil_id` int(11) DEFAULT NULL AFTER `senha`,
  ADD COLUMN `clinica_id` int(11) DEFAULT NULL AFTER `perfil_id`, -- For multi-clinic users
  ADD COLUMN `parent_id` int(11) DEFAULT NULL AFTER `clinica_id`; -- For hierarchy

-- Migrate existing data (Mapping ENUM to ID)
UPDATE `usuarios` SET `perfil_id` = 1 WHERE `nivel_acesso` = 'admin';
UPDATE `usuarios` SET `perfil_id` = 2 WHERE `nivel_acesso` = 'medico';
UPDATE `usuarios` SET `perfil_id` = 3 WHERE `nivel_acesso` = 'recepcionista';

-- Now we can drop the old column (Optional, keeping for safety for now, or assume we drop it)
-- ALTER TABLE `usuarios` DROP COLUMN `nivel_acesso`;

-- Add Constraints
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_user_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`),
  ADD CONSTRAINT `fk_user_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas_parceiras` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_parent` FOREIGN KEY (`parent_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

-- --------------------------------------------------------
-- 4. Modify Procedures Prices (Cost vs Sale)
-- --------------------------------------------------------

ALTER TABLE `valores_procedimentos`
  CHANGE `valor` `valor_paciente` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  ADD COLUMN `valor_repasse` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `valor_paciente`;

-- Initialize valor_repasse with a default (e.g. 50% of patient price or 0)
UPDATE `valores_procedimentos` SET `valor_repasse` = `valor_paciente` * 0.5;

COMMIT;
