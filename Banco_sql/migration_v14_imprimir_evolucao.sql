-- Migration V14 - Suporte a Impressão de Evolução e Painel Profissional
-- Data: 16/02/2026

-- 1. Atualizar status dos agendamentos para suportar 'realizado'
ALTER TABLE master_agendamentos MODIFY COLUMN status ENUM('agendado', 'confirmado', 'cancelado', 'realizado', 'falta') DEFAULT 'agendado';

-- 2. Inserir novas permissões
-- Tabela: permissoes (nome, chave, descricao)
INSERT IGNORE INTO `permissoes` (`nome`, `chave`, `descricao`) VALUES
('Acesso ao Painel do Profissional', 'painel_profissional', 'Permite acesso ao painel exclusivo do médico/profissional'),
('Visualizar Prontuário (Minha Clínica)', 'minha_clinica_pacientes', 'Permite visualizar prontuários de pacientes da clínica'),
('Imprimir Evolução', 'imprimir_evolucao', 'Permite imprimir evoluções clínicas');

-- 3. Associar permissões ao perfil Administrador (ID 1) para garantir acesso inicial
-- Tabela: perfil_permissoes (perfil_id, permissao_id)
INSERT IGNORE INTO `perfil_permissoes` (`perfil_id`, `permissao_id`)
SELECT 1, id FROM `permissoes` WHERE `chave` IN (
    'painel_profissional', 
    'minha_clinica_pacientes', 
    'imprimir_evolucao'
);

-- Nota: Para o perfil 'Médico' ou 'Profissional', o administrador deve associar manualmente pelo painel de Perfis.
