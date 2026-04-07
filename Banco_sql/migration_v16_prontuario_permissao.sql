-- Migration V16 - Permissão para Visualizar Prontuário Completo
-- Data: 07/04/2026

-- 1. Inserir nova permissão
INSERT IGNORE INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Ver Prontuário Completo', 'ver_prontuario', 'Permite visualizar e imprimir o prontuário completo do paciente (todas as evoluções)');

-- 2. Conceder automaticamente ao perfil Administrador (ID 1)
INSERT IGNORE INTO `perfil_permissoes` (`perfil_id`, `permissao_id`)
SELECT 1, id FROM `permissoes` WHERE `chave` = 'ver_prontuario';

-- Nota: Para liberar a outros perfis (médico, recepcionista, etc.),
-- o administrador deve associar manualmente pelo painel de Perfis.
