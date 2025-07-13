-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13-Jul-2025 às 19:48
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

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
  `ultima_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `paciente_id`, `clinica_id`, `especialidade_id`, `procedimento_id`, `data_consulta`, `hora_consulta`, `status_agendamento`, `observacoes`, `data_agendamento`, `ultima_atualizacao`) VALUES
(43, 3, 6, 9, 31, '2025-06-28', '15:00:00', 'agendado', 'xczvxzvzxvzxvzxvxz', '2025-06-01 16:38:27', '2025-06-01 16:38:27');

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
(11, 'dias_atendimento', '1,2,3,4,5', 'lista', 'Dias da semana com atendimento (1=Segunda a 7=Domingo)', '2025-05-01 03:12:39');

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
(11, 6, 10, NULL, 1);

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
(56, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 29/06/2025 às 08:30:00', 'index.php?module=agendamentos&action=view&id=45', 0, NULL, '2025-06-01 14:31:41');

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
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` enum('admin','recepcionista','medico') NOT NULL,
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `ultimo_acesso`, `status`, `data_cadastro`) VALUES
(5, 'Administrador', 'hueltonti@gmail.com', '$2y$10$c/nR/6KjVlTk1WBAl16oTOmbGBurOGcUAOHwZT9yG3YfthS/5AAua', 'admin', '2025-07-13 17:33:18', 1, '2025-05-08 23:34:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `valores_procedimentos`
--

CREATE TABLE `valores_procedimentos` (
  `id` int(11) NOT NULL,
  `especialidade_id` int(11) NOT NULL,
  `procedimento` varchar(150) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `valores_procedimentos`
--

INSERT INTO `valores_procedimentos` (`id`, `especialidade_id`, `procedimento`, `valor`, `status`) VALUES
(24, 9, 'Eletrocardiograma', 80.00, 1),
(25, 9, 'Ecocardiograma', 350.00, 1),
(26, 9, 'Teste Ergométrico', 250.00, 1),
(27, 9, 'Holter 24 horas', 200.00, 1),
(28, 9, 'MAPA - Monitorização Ambulatorial da Pressão Arterial', 150.00, 1),
(29, 9, 'Ecocardiograma com Doppler', 450.00, 1),
(30, 9, 'Cateterismo Cardíaco', 2500.00, 1),
(31, 9, 'Angioplastia', 8000.00, 1),
(32, 9, 'Consulta Cardiológica', 300.00, 1),
(33, 9, 'Retorno de Consulta', 150.00, 1),
(34, 9, 'Eletrocardiograma de Esforço', 180.00, 1),
(35, 9, 'Ecocardiograma Transesofágico', 800.00, 1),
(36, 9, 'Cintilografia Miocárdica', 1200.00, 1),
(37, 9, 'Ressonância Magnética Cardíaca', 1500.00, 1),
(38, 9, 'Tomografia Cardíaca', 1000.00, 1),
(39, 9, 'Score de Cálcio Coronariano', 400.00, 1),
(40, 9, 'Angiotomografia Coronariana', 1200.00, 1),
(41, 9, 'Teste de Inclinação (Tilt Test)', 500.00, 1),
(42, 9, 'Estudo Eletrofisiológico', 3000.00, 1),
(43, 9, 'Ablação por Radiofrequência', 15000.00, 1),
(44, 10, 'Joelho', 98.00, 1),
(45, 10, 'Tornozelo', 115.00, 1),
(46, 11, 'RX', 50.00, 1),
(47, 11, 'rx joelho direito', 100.00, 1),
(48, 11, 'rx joelho esquerdo', 60.00, 1),
(49, 11, 'rx mão direita', 60.00, 1);

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
-- Índices para tabela `procedimentos_clinicas`
--
ALTER TABLE `procedimentos_clinicas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `clinicas_parceiras`
--
ALTER TABLE `clinicas_parceiras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `especialidades_clinicas`
--
ALTER TABLE `especialidades_clinicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `guias_encaminhamento`
--
ALTER TABLE `guias_encaminhamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `procedimentos_clinicas`
--
ALTER TABLE `procedimentos_clinicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `valores_procedimentos`
--
ALTER TABLE `valores_procedimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
-- Limitadores para a tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `valores_procedimentos`
--
ALTER TABLE `valores_procedimentos`
  ADD CONSTRAINT `valores_procedimentos_ibfk_1` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
