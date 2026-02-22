-- Migration V4 - Sistema de Logs

CREATE TABLE IF NOT EXISTS `logs_sistema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nome` varchar(100) DEFAULT NULL,
  `acao` varchar(50) NOT NULL COMMENT 'criar, editar, excluir, login, logout, etc',
  `modulo` varchar(50) NOT NULL COMMENT 'usuarios, perfis, agendamentos, etc',
  `descricao` text NOT NULL,
  `registro_id` int(11) DEFAULT NULL COMMENT 'ID do registro afetado',
  `dados_anteriores` json DEFAULT NULL COMMENT 'Estado antes da alteração',
  `dados_novos` json DEFAULT NULL COMMENT 'Estado após a alteração',
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `data_hora` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_acao` (`acao`),
  KEY `idx_modulo` (`modulo`),
  KEY `idx_data_hora` (`data_hora`),
  KEY `idx_registro` (`modulo`, `registro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adiciona coluna foto na tabela usuarios se não existir
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `foto` VARCHAR(255) DEFAULT NULL AFTER `email`;
