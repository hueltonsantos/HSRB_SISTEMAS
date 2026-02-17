-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04-Fev-2026 às 06:02
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `clinica_encaminhamento`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `clinica_id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `procedimento_id` int(11) DEFAULT NULL,
  `data_consulta` date NOT NULL,
  `hora_consulta` time NOT NULL,
  `status_agendamento` enum('agendado','confirmado','realizado','cancelado') DEFAULT 'agendado',
  `observacoes` text DEFAULT NULL,
  `data_agendamento` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `forma_pagamento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `paciente_id`, `clinica_id`, `especialidade_id`, `procedimento_id`, `data_consulta`, `hora_consulta`, `status_agendamento`, `observacoes`, `data_agendamento`, `ultima_atualizacao`, `valor_total`, `forma_pagamento`) VALUES
(43, 3, 6, 9, 31, '2025-06-28', '15:00:00', 'confirmado', 'xczvxzvzxvzxvzxvxz', '2025-06-01 16:38:27', '2026-02-04 03:21:47', 0.00, NULL),
(46, 4, 5, 10, 44, '2026-02-04', '15:00:00', 'agendado', '', '2026-02-04 02:07:14', '2026-02-04 02:07:14', 0.00, 'Dinheiro'),
(47, 3, 6, 9, 24, '2026-02-04', '11:00:00', 'confirmado', 'testeste', '2026-02-04 02:29:21', '2026-02-04 02:29:21', 808000.00, 'Dinheiro'),
(48, 3, 5, 10, 44, '2026-02-05', '11:30:00', 'agendado', 'tetawrrewr', '2026-02-04 02:33:57', '2026-02-04 02:33:57', 21300.00, 'PIX'),
(49, 4, 4, 9, 39, '2026-02-13', '08:00:00', 'confirmado', 'testeste', '2026-02-04 02:35:29', '2026-02-04 02:35:29', 60000.00, 'Cartão de Crédito'),
(50, 3, 5, 10, 44, '2026-02-19', '09:30:00', 'realizado', '', '2026-02-04 02:38:53', '2026-02-04 04:14:01', 21300.00, 'Cartão de Débito'),
(51, 3, 5, 10, 44, '2026-02-05', '15:00:00', 'confirmado', 'trdtrdt', '2026-02-04 04:51:45', '2026-02-04 04:51:45', 21300.00, 'Dinheiro'),
(52, 4, 5, 10, 44, '2026-02-19', '08:30:00', 'confirmado', '', '2026-02-04 04:55:01', '2026-02-04 04:55:01', 21300.00, 'Dinheiro'),
(53, 4, 4, 9, 39, '2026-02-20', '11:30:00', 'agendado', '', '2026-02-04 04:56:32', '2026-02-04 04:56:32', 40000.00, 'Cartão de Crédito');

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendamento_procedimentos`
--

CREATE TABLE `agendamento_procedimentos` (
  `id` int(11) NOT NULL,
  `agendamento_id` int(11) NOT NULL,
  `procedimento_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `agendamento_procedimentos`
--

INSERT INTO `agendamento_procedimentos` (`id`, `agendamento_id`, `procedimento_id`, `valor`, `created_at`) VALUES
(1, 46, 44, 98.00, '2026-02-04 02:07:14'),
(2, 47, 24, 80.00, '2026-02-04 02:29:21'),
(3, 47, 31, 8000.00, '2026-02-04 02:29:21'),
(4, 48, 44, 98.00, '2026-02-04 02:33:57'),
(5, 48, 45, 115.00, '2026-02-04 02:33:57'),
(6, 49, 39, 400.00, '2026-02-04 02:35:29'),
(7, 49, 27, 200.00, '2026-02-04 02:35:29'),
(8, 50, 44, 98.00, '2026-02-04 02:38:53'),
(9, 50, 45, 115.00, '2026-02-04 02:38:53'),
(10, 51, 44, 98.00, '2026-02-04 04:51:45'),
(11, 51, 45, 115.00, '2026-02-04 04:51:45'),
(12, 52, 44, 98.00, '2026-02-04 04:55:01'),
(13, 52, 45, 115.00, '2026-02-04 04:55:01'),
(14, 53, 39, 400.00, '2026-02-04 04:56:32');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clinicas_parceiras`
--

CREATE TABLE `clinicas_parceiras` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `razao_social` varchar(150) DEFAULT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `responsavel` varchar(100) DEFAULT NULL,
  `endereco` varchar(150) NOT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `telefone` varchar(15) NOT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `site` varchar(100) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `clinicas_parceiras`
--

INSERT INTO `clinicas_parceiras` (`id`, `nome`, `razao_social`, `cnpj`, `responsavel`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `cep`, `telefone`, `celular`, `email`, `site`, `data_cadastro`, `ultima_atualizacao`, `status`) VALUES
(4, 'Procordis Unidade Cardiologica de Vit da Conquista LTDA', 'Procordis Unidade Cardiologica de Vit da Conquista LTDA', '32.672.941/0001-68', 'Sociedade Empresária Limitada', 'Rua Guilhermino Novais', '182', '', 'Recreio', 'Vitória da Conquista', 'BA', '45020-600', '774245588', '', '', '', '2025-05-25 18:15:16', '2025-05-25 18:15:16', 1),
(5, 'Htec Store', 'Huelton Santos', '33.720.529/0001-39', 'huelton', 'rua teste', '9000', '', 'Candeias', 'Vitória da Conquista', 'BA', '40029-268', '7799882930', '', '', '', '2025-05-31 19:16:20', '2025-05-31 19:16:20', 1),
(6, 'clinica teste samur', 'samur', '16.205.262/0001-22', 'miqueias', 'Rua Panamá', '380', 'hospital', 'Jurema', 'Vitória da Conquista', 'BA', '45023-145', '7721028400', '', '', '', '2025-05-31 22:00:22', '2025-05-31 22:00:22', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'texto',
  `descricao` text DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `descricao`, `data_atualizacao`) VALUES
(1, 'nome_clinica', 'Clínica de Encaminhamento', 'texto', 'Nome da clínica', '2025-05-01 03:12:39'),
(2, 'endereco_clinica', 'Endereço da Clínica', 'texto', 'Endereço completo da clínica', '2025-05-01 03:12:39'),
(3, 'telefone_clinica', '(00) 0000-0000', 'texto', 'Telefone principal da clínica', '2025-05-01 03:12:39'),
(4, 'email_clinica', 'contato@clinica.com', 'texto', 'E-mail de contato da clínica', '2025-05-01 03:12:39'),
(5, 'logo', '', 'arquivo', 'Logo da clínica', '2025-05-01 03:12:39'),
(6, 'qtd_itens_paginacao', '10', 'numero', 'Quantidade de itens por página nas listagens', '2025-05-01 03:12:39'),
(7, 'permitir_agendamento_simultaneo', '0', 'booleano', 'Permitir agendamento de consultas em horários simultâneos', '2025-05-01 03:12:39'),
(8, 'intervalo_consultas', '30', 'numero', 'Intervalo entre consultas em minutos', '2025-05-01 03:12:39'),
(9, 'hora_inicio_atendimento', '08:00', 'hora', 'Hora de início do atendimento', '2025-05-01 03:12:39'),
(10, 'hora_fim_atendimento', '18:00', 'hora', 'Hora de término do atendimento', '2025-05-01 03:12:39'),
(11, 'dias_atendimento', '1,2,3,4,5', 'lista', 'Dias da semana com atendimento (1=Segunda a 7=Domingo)', '2025-05-01 03:12:39'),
(12, 'percentual_repasse_padrao', '50', 'numero', 'Percentual padrão de repasse (%)', '2026-02-04 01:48:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `especialidades`
--

INSERT INTO `especialidades` (`id`, `nome`, `descricao`, `status`) VALUES
(9, 'Cardiologia', 'Testando cadastro de especialidades', 1),
(10, 'Pediatria', 'teste', 1),
(11, 'ortopedia', 'teste', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `especialidades_clinicas`
--

CREATE TABLE `especialidades_clinicas` (
  `id` int(11) NOT NULL,
  `clinica_id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `especialidades_clinicas`
--

INSERT INTO `especialidades_clinicas` (`id`, `clinica_id`, `especialidade_id`, `observacoes`, `status`) VALUES
(8, 4, 9, NULL, 1),
(9, 5, 10, NULL, 1),
(10, 6, 9, NULL, 1),
(11, 6, 10, NULL, 1),
(12, 6, 11, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `guias_encaminhamento`
--

CREATE TABLE `guias_encaminhamento` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `procedimento_id` int(11) NOT NULL,
  `data_agendamento` date NOT NULL,
  `horario_agendamento` time DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('agendado','realizado','cancelado') NOT NULL DEFAULT 'agendado',
  `data_emissao` datetime NOT NULL,
  `codigo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `guias_encaminhamento`
--

INSERT INTO `guias_encaminhamento` (`id`, `paciente_id`, `procedimento_id`, `data_agendamento`, `horario_agendamento`, `observacoes`, `status`, `data_emissao`, `codigo`) VALUES
(29, 4, 44, '2026-02-04', '15:00:00', '', 'agendado', '2026-02-03 23:09:15', 'G202602032505'),
(30, 4, 44, '2026-02-04', '15:00:00', '', 'agendado', '2026-02-03 23:10:25', 'G202602039128'),
(31, 3, 24, '2026-02-04', '11:00:00', 'testestetesttestest', 'agendado', '2026-02-03 23:30:34', 'G202602030514'),
(32, 3, 24, '2026-02-04', '11:00:00', 'testestetesttestest', 'agendado', '2026-02-03 23:31:24', 'G202602035882'),
(33, 4, 24, '2026-02-13', '08:00:00', 'tsetetsetset\n\nMotivo do cancelamento: testetse', 'cancelado', '2026-02-03 23:35:44', 'G202602035587'),
(34, 3, 44, '2026-02-19', '09:30:00', 'twatwatwatwatawtwataw', 'agendado', '2026-02-03 23:39:04', 'G202602039151'),
(35, 3, 44, '2026-02-05', '15:00:00', '', 'agendado', '2026-02-04 01:51:54', 'G202602045655'),
(36, 4, 44, '2026-02-19', '08:30:00', '', 'agendado', '2026-02-04 01:55:06', 'G202602041899'),
(37, 4, 44, '2026-02-19', '08:30:00', '', 'agendado', '2026-02-04 01:55:12', 'G202602040711'),
(38, 4, 44, '2026-02-19', '08:30:00', '', 'agendado', '2026-02-04 01:55:19', 'G202602041559'),
(39, 4, 24, '2026-02-20', '11:30:00', 'ggdgdgd', 'agendado', '2026-02-04 01:56:38', 'G202602045811'),
(40, 4, 24, '2026-02-20', '11:30:00', '', 'agendado', '2026-02-04 01:58:09', 'G202602041595'),
(41, 4, 24, '2026-02-20', '11:30:00', '', 'agendado', '2026-02-04 01:59:56', 'G202602042524'),
(42, 4, 24, '2026-02-20', '11:30:00', '', 'agendado', '2026-02-04 02:00:38', 'G202602044804'),
(43, 4, 24, '2026-02-20', '11:30:00', '', 'agendado', '2026-02-04 02:01:04', 'G202602042358'),
(44, 4, 24, '2026-02-20', '11:30:00', '', 'agendado', '2026-02-04 02:01:28', 'G202602043863');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs_sistema`
--

CREATE TABLE `logs_sistema` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nome` varchar(100) DEFAULT NULL,
  `acao` varchar(50) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `dados_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_anteriores`)),
  `dados_novos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_novos`)),
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `data_hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `logs_sistema`
--

INSERT INTO `logs_sistema` (`id`, `usuario_id`, `usuario_nome`, `acao`, `modulo`, `descricao`, `registro_id`, `dados_anteriores`, `dados_novos`, `ip`, `user_agent`, `data_hora`) VALUES
(1, 5, 'Administrador', 'login', 'auth', 'Login realizado com sucesso', NULL, NULL, NULL, '192.168.100.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 01:46:02'),
(2, 5, 'Administrador', 'editar', 'perfis', 'Perfil \'Recepcionista\' atualizado', 3, NULL, '{\"nome\":\"Recepcionista\",\"permissoes\":[\"5\",\"1\"]}', '192.168.100.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 01:49:28'),
(3, 5, 'Administrador', 'editar', 'perfis', 'Perfil \'Recepcionista\' atualizado', 3, NULL, '{\"nome\":\"Recepcionista\",\"permissoes\":[\"4\",\"3\"]}', '192.168.100.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 01:50:56'),
(4, 8, 'huelton', 'logout', 'auth', 'Logout realizado', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 01:55:37'),
(5, 5, 'Administrador', 'logout', 'auth', 'Logout realizado', NULL, NULL, NULL, '192.168.100.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 01:55:57'),
(6, 5, 'Administrador', 'login', 'auth', 'Login realizado com sucesso', NULL, NULL, NULL, '192.168.100.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 01:56:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `log_atividades`
--

CREATE TABLE `log_atividades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `icone` varchar(50) NOT NULL,
  `cor` varchar(20) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `usuario_id` int(11) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `tipo`, `icone`, `cor`, `titulo`, `mensagem`, `link`, `lida`, `usuario_id`, `data_criacao`) VALUES
(29, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 29/05/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=19', 0, NULL, '2025-05-26 18:32:50'),
(30, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 28/05/2025 às 15:00:00', 'index.php?module=agendamentos&action=view&id=20', 0, NULL, '2025-05-26 18:43:45'),
(31, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 29/05/2025 às 10:30:00', 'index.php?module=agendamentos&action=view&id=21', 0, NULL, '2025-05-26 19:14:20'),
(32, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 30/05/2025 às 14:00:00', 'index.php?module=agendamentos&action=view&id=22', 0, NULL, '2025-05-26 19:15:06'),
(33, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 29/05/2025 às 17:00:00', 'index.php?module=agendamentos&action=view&id=23', 0, NULL, '2025-05-26 19:22:20'),
(34, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 31/05/2025 às 10:30:00', 'index.php?module=agendamentos&action=view&id=24', 0, NULL, '2025-05-26 19:24:09'),
(35, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 28/05/2025 às 08:30:00', 'index.php?module=agendamentos&action=view&id=25', 0, NULL, '2025-05-26 19:26:55'),
(36, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 31/05/2025 às 14:00:00', 'index.php?module=agendamentos&action=view&id=26', 0, NULL, '2025-05-27 22:30:42'),
(37, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 06/06/2025 às 11:00:00', 'index.php?module=agendamentos&action=view&id=27', 0, NULL, '2025-05-31 15:52:24'),
(38, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 20/06/2025 às 16:00:00', 'index.php?module=agendamentos&action=view&id=28', 0, NULL, '2025-05-31 15:54:20'),
(39, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 07/06/2025 às 15:30:00', 'index.php?module=agendamentos&action=view&id=29', 0, NULL, '2025-05-31 17:29:35'),
(40, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Carla Meira dos Santos foi cancelado', 'index.php?module=agendamentos&action=view&id=29', 0, NULL, '2025-05-31 19:44:22'),
(41, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 06/06/2025 às 15:30:00', 'index.php?module=agendamentos&action=view&id=30', 0, NULL, '2025-05-31 20:35:14'),
(42, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 06/06/2025 às 16:00:00', 'index.php?module=agendamentos&action=view&id=31', 0, NULL, '2025-05-31 20:37:22'),
(43, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 05/06/2025 às 16:00:00', 'index.php?module=agendamentos&action=view&id=32', 0, NULL, '2025-05-31 20:37:58'),
(44, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 05/06/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=33', 0, NULL, '2025-05-31 20:39:36'),
(45, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 05/06/2025 às 15:30:00', 'index.php?module=agendamentos&action=view&id=34', 0, NULL, '2025-05-31 20:41:36'),
(46, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 05/06/2025 às 14:30:00', 'index.php?module=agendamentos&action=view&id=35', 0, NULL, '2025-05-31 20:49:01'),
(47, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 06/06/2025 às 16:00:00', 'index.php?module=agendamentos&action=view&id=36', 0, NULL, '2025-05-31 21:42:38'),
(48, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 06/06/2025 às 14:30:00', 'index.php?module=agendamentos&action=view&id=37', 0, NULL, '2025-05-31 21:43:55'),
(49, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 06/06/2025 às 15:30:00', 'index.php?module=agendamentos&action=view&id=38', 0, NULL, '2025-05-31 21:45:46'),
(50, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 06/06/2025 às 15:30:00', 'index.php?module=agendamentos&action=view&id=39', 0, NULL, '2025-05-31 21:49:13'),
(51, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 06/06/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=40', 0, NULL, '2025-05-31 21:54:56'),
(52, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 06/06/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=41', 0, NULL, '2025-05-31 22:00:36'),
(53, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 07/06/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=42', 0, NULL, '2025-05-31 22:11:53'),
(54, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 28/06/2025 às 15:00:00', 'index.php?module=agendamentos&action=view&id=43', 0, NULL, '2025-06-01 13:38:27'),
(55, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 26/06/2025 às 17:00:00', 'index.php?module=agendamentos&action=view&id=44', 0, NULL, '2025-06-01 13:57:25'),
(56, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 29/06/2025 às 08:30:00', 'index.php?module=agendamentos&action=view&id=45', 0, NULL, '2025-06-01 14:31:41'),
(57, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 04/02/2026 às 15:00:00', 'index.php?module=agendamentos&action=view&id=46', 0, NULL, '2026-02-03 23:07:14'),
(58, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 04/02/2026 às 11:00:00', 'index.php?module=agendamentos&action=view&id=47', 0, NULL, '2026-02-03 23:29:21'),
(59, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 05/02/2026 às 11:30:00', 'index.php?module=agendamentos&action=view&id=48', 0, NULL, '2026-02-03 23:33:57'),
(60, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 13/02/2026 às 08:00:00', 'index.php?module=agendamentos&action=view&id=49', 0, NULL, '2026-02-03 23:35:29'),
(61, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 19/02/2026 às 09:30:00', 'index.php?module=agendamentos&action=view&id=50', 0, NULL, '2026-02-03 23:38:53'),
(62, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 05/02/2026 às 15:00:00', 'index.php?module=agendamentos&action=view&id=51', 0, NULL, '2026-02-04 01:51:45'),
(63, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 19/02/2026 às 08:30:00', 'index.php?module=agendamentos&action=view&id=52', 0, NULL, '2026-02-04 01:55:01'),
(64, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente jesse alves foi criado para 20/02/2026 às 11:30:00', 'index.php?module=agendamentos&action=view&id=53', 0, NULL, '2026-02-04 01:56:32');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `sexo` enum('M','F','O') NOT NULL,
  `endereco` varchar(150) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `telefone_fixo` varchar(15) DEFAULT NULL,
  `celular` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `convenio` varchar(50) DEFAULT NULL,
  `numero_carteirinha` varchar(30) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `pacientes`
--

INSERT INTO `pacientes` (`id`, `nome`, `data_nascimento`, `cpf`, `rg`, `sexo`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `cep`, `telefone_fixo`, `celular`, `email`, `convenio`, `numero_carteirinha`, `observacoes`, `data_cadastro`, `ultima_atualizacao`, `status`) VALUES
(3, 'Carla Meira dos Santos', '1992-10-24', '125.684.960-02', '1637444456', 'M', 'Rua Carlos Alberto Figueredo', '900', 'teste', 'Candeias', 'Vitória da Conquista', 'BA', '45029-268', '', '77999882931', '', '', '', '', '2025-05-07 03:13:53', '2025-05-07 03:13:53', 1),
(4, 'jesse alves', '1986-05-09', '123.456.789-09', '', 'M', 'Praça Padre Benedito Soares', '55', 'apt', 'Centro', 'Vitória da Conquista', 'BA', '45000-175', '7798879211', '7798879211', 'hueltonti@gmail.com', 'teste', '34634', 'teste', '2025-05-31 21:57:48', '2025-05-31 21:57:48', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `perfil_permissoes`
--

CREATE TABLE `perfil_permissoes` (
  `perfil_id` int(11) NOT NULL,
  `permissao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `perfil_permissoes`
--

INSERT INTO `perfil_permissoes` (`perfil_id`, `permissao_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(3, 3),
(3, 4),
(4, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `perfis`
--

CREATE TABLE `perfis` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `perfis`
--

INSERT INTO `perfis` (`id`, `nome`, `descricao`, `status`) VALUES
(1, 'Administrador', 'Acesso total ao sistema', 1),
(2, 'Médico', 'Acesso a agendas e prontuários', 1),
(3, 'Recepcionista', 'Acesso a agendamentos e cadastros básicos', 1),
(4, 'teste', '', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `permissoes`
--

INSERT INTO `permissoes` (`id`, `nome`, `chave`, `descricao`) VALUES
(1, 'Gerenciar Usuários', 'user_manage', 'Criar, editar e remover usuários'),
(2, 'Gerenciar Perfis', 'role_manage', 'Criar e editar perfis de acesso'),
(3, 'Visualizar Agendamentos', 'appointment_view', 'Ver agenda'),
(4, 'Criar Agendamento', 'appointment_create', 'Marcar consultas'),
(5, 'Gerenciar Preços', 'price_manage', 'Editar valores de procedimentos'),
(6, 'Visualizar Relatórios', 'report_view', 'Acessar área de relatórios');

-- --------------------------------------------------------

--
-- Estrutura da tabela `procedimentos_clinicas`
--

CREATE TABLE `procedimentos_clinicas` (
  `id` int(11) NOT NULL,
  `procedimento_id` int(11) DEFAULT NULL,
  `clinica_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil_id` int(11) DEFAULT NULL,
  `clinica_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `nivel_acesso` enum('admin','recepcionista','medico') NOT NULL,
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `foto`, `senha`, `perfil_id`, `clinica_id`, `parent_id`, `nivel_acesso`, `ultimo_acesso`, `status`, `data_cadastro`) VALUES
(5, 'Administrador', 'hueltonti@gmail.com', NULL, '$2y$10$c/nR/6KjVlTk1WBAl16oTOmbGBurOGcUAOHwZT9yG3YfthS/5AAua', 1, NULL, NULL, 'admin', '2026-02-04 04:56:02', 1, '2025-05-08 23:34:39'),
(7, 'teste', 'teste@gmail.com', NULL, '$2y$10$QZeUv4BbjQcQyM4.ReOGseIfkRHNKh9LdhWqFq2D.ai9YrVvC/zkS', 3, NULL, NULL, 'admin', '2026-02-04 03:47:57', 1, '2026-02-04 03:35:36'),
(8, 'huelton', 'teste1@gmail.com', NULL, '$2y$10$pmuzHZNFlGqknrSwRCGB6OWb2VZUCtaCI8MQjI67XAa5wR/u5QDVu', 3, NULL, 7, 'admin', '2026-02-04 04:41:10', 1, '2026-02-04 03:50:16');

-- --------------------------------------------------------

--
-- Estrutura da tabela `valores_procedimentos`
--

CREATE TABLE `valores_procedimentos` (
  `id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `procedimento` varchar(150) NOT NULL,
  `valor_paciente` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_repasse` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `valores_procedimentos`
--

INSERT INTO `valores_procedimentos` (`id`, `especialidade_id`, `procedimento`, `valor_paciente`, `valor_repasse`, `status`) VALUES
(24, 9, 'Eletrocardiograma', 80.00, 40.00, 1),
(25, 9, 'Ecocardiograma', 350.00, 175.00, 1),
(26, 9, 'Teste Ergométrico', 250.00, 125.00, 1),
(27, 9, 'Holter 24 horas', 200.00, 100.00, 1),
(28, 9, 'MAPA - Monitorização Ambulatorial da Pressão Arterial', 150.00, 75.00, 1),
(29, 9, 'Ecocardiograma com Doppler', 450.00, 225.00, 1),
(30, 9, 'Cateterismo Cardíaco', 2500.00, 1250.00, 1),
(31, 9, 'Angioplastia', 8000.00, 4000.00, 1),
(32, 9, 'Consulta Cardiológica', 300.00, 150.00, 1),
(33, 9, 'Retorno de Consulta', 150.00, 75.00, 1),
(34, 9, 'Eletrocardiograma de Esforço', 180.00, 90.00, 1),
(35, 9, 'Ecocardiograma Transesofágico', 800.00, 400.00, 1),
(36, 9, 'Cintilografia Miocárdica', 1200.00, 600.00, 1),
(37, 9, 'Ressonância Magnética Cardíaca', 1500.00, 750.00, 1),
(38, 9, 'Tomografia Cardíaca', 1000.00, 500.00, 1),
(39, 9, 'Score de Cálcio Coronariano', 400.00, 200.00, 1),
(40, 9, 'Angiotomografia Coronariana', 1200.00, 600.00, 1),
(41, 9, 'Teste de Inclinação (Tilt Test)', 500.00, 250.00, 1),
(42, 9, 'Estudo Eletrofisiológico', 3000.00, 1500.00, 1),
(43, 9, 'Ablação por Radiofrequência', 180.00, 95.00, 1),
(44, 10, 'Joelho', 98.00, 49.00, 1),
(45, 10, 'Tornozelo', 115.00, 57.50, 1),
(46, 11, 'RX', 50.00, 25.00, 1),
(47, 11, 'rx joelho direito', 100.00, 50.00, 1),
(48, 11, 'rx joelho esquerdo', 60.00, 30.00, 1),
(49, 11, 'rx mão direita', 60.00, 30.00, 1),
(51, 11, 'tsetetset', 800.00, 90000.00, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `clinica_id` (`clinica_id`),
  ADD KEY `especialidade_id` (`especialidade_id`),
  ADD KEY `fk_agendamento_procedimento` (`procedimento_id`);

--
-- Índices para tabela `agendamento_procedimentos`
--
ALTER TABLE `agendamento_procedimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendamento_id` (`agendamento_id`),
  ADD KEY `procedimento_id` (`procedimento_id`);

--
-- Índices para tabela `clinicas_parceiras`
--
ALTER TABLE `clinicas_parceiras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices para tabela `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `especialidades_clinicas`
--
ALTER TABLE `especialidades_clinicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinica_id` (`clinica_id`),
  ADD KEY `especialidade_id` (`especialidade_id`);

--
-- Índices para tabela `guias_encaminhamento`
--
ALTER TABLE `guias_encaminhamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_guia_paciente` (`paciente_id`),
  ADD KEY `fk_guia_procedimento` (`procedimento_id`);

--
-- Índices para tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `log_atividades`
--
ALTER TABLE `log_atividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_usuario` (`usuario_id`);

--
-- Índices para tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices para tabela `perfil_permissoes`
--
ALTER TABLE `perfil_permissoes`
  ADD PRIMARY KEY (`perfil_id`,`permissao_id`),
  ADD KEY `fk_pp_permissao` (`permissao_id`);

--
-- Índices para tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices para tabela `procedimentos_clinicas`
--
ALTER TABLE `procedimentos_clinicas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_perfil` (`perfil_id`),
  ADD KEY `fk_user_clinica` (`clinica_id`),
  ADD KEY `fk_user_parent` (`parent_id`);

--
-- Índices para tabela `valores_procedimentos`
--
ALTER TABLE `valores_procedimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidade_id` (`especialidade_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de tabela `agendamento_procedimentos`
--
ALTER TABLE `agendamento_procedimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `clinicas_parceiras`
--
ALTER TABLE `clinicas_parceiras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `especialidades_clinicas`
--
ALTER TABLE `especialidades_clinicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `guias_encaminhamento`
--
ALTER TABLE `guias_encaminhamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `log_atividades`
--
ALTER TABLE `log_atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `procedimentos_clinicas`
--
ALTER TABLE `procedimentos_clinicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `valores_procedimentos`
--
ALTER TABLE `valores_procedimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas_parceiras` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_3` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`),
  ADD CONSTRAINT `fk_agendamento_procedimento` FOREIGN KEY (`procedimento_id`) REFERENCES `valores_procedimentos` (`id`);

--
-- Limitadores para a tabela `especialidades_clinicas`
--
ALTER TABLE `especialidades_clinicas`
  ADD CONSTRAINT `especialidades_clinicas_ibfk_1` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas_parceiras` (`id`),
  ADD CONSTRAINT `especialidades_clinicas_ibfk_2` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);

--
-- Limitadores para a tabela `guias_encaminhamento`
--
ALTER TABLE `guias_encaminhamento`
  ADD CONSTRAINT `fk_guia_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guia_procedimento` FOREIGN KEY (`procedimento_id`) REFERENCES `valores_procedimentos` (`id`);

--
-- Limitadores para a tabela `log_atividades`
--
ALTER TABLE `log_atividades`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `perfil_permissoes`
--
ALTER TABLE `perfil_permissoes`
  ADD CONSTRAINT `fk_pp_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_permissao` FOREIGN KEY (`permissao_id`) REFERENCES `permissoes` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_user_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas_parceiras` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_parent` FOREIGN KEY (`parent_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`);

--
-- Limitadores para a tabela `valores_procedimentos`
--
ALTER TABLE `valores_procedimentos`
  ADD CONSTRAINT `valores_procedimentos_ibfk_1` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
