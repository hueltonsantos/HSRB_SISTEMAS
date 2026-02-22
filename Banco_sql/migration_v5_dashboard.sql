-- Migration V5: Dashboard em Tempo Real
-- Data: 2026-02-08

-- Adicionar nova permissão para Dashboard em Tempo Real
INSERT INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Dashboard Tempo Real', 'dashboard_realtime', 'Acessar painel de relatórios em tempo real com gráficos e exportação');

-- Conceder permissão ao perfil Administrador (id=1)
INSERT INTO `perfil_permissoes` (`perfil_id`, `permissao_id`)
SELECT 1, id FROM `permissoes` WHERE chave = 'dashboard_realtime';

-- Garantir que a coluna forma_pagamento existe na tabela agendamentos
-- (já deve existir, mas garante compatibilidade)
-- ALTER TABLE `agendamentos` ADD COLUMN IF NOT EXISTS `forma_pagamento` VARCHAR(50) DEFAULT NULL;
-- ALTER TABLE `agendamentos` ADD COLUMN IF NOT EXISTS `valor_total` DECIMAL(10,2) DEFAULT 0.00;
