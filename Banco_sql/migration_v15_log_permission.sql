-- Adicionar permissão de visualização de logs e configurações
INSERT INTO permissoes (chave, nome, descricao) VALUES 
('log_view', 'Visualizar Logs', 'Permite acessar o log de atividades do sistema'),
('config_view', 'Visualizar Configurações', 'Permite acessar as configurações do sistema')
ON DUPLICATE KEY UPDATE nome = VALUES(nome), descricao = VALUES(descricao);

-- Dar permissão ao Administrador (ID 1)
INSERT IGNORE INTO perfil_permissoes (perfil_id, permissao_id)
SELECT 1, id FROM permissoes WHERE chave IN ('log_view', 'config_view');
