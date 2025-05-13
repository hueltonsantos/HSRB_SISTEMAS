-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09-Maio-2025 às 16:18
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

INSERT INTO `agendamentos` (`id`, `paciente_id`, `clinica_id`, `especialidade_id`, `data_consulta`, `hora_consulta`, `status_agendamento`, `observacoes`, `data_agendamento`, `ultima_atualizacao`) VALUES
(1, 2, 2, 3, '2025-05-15', '15:30:00', 'cancelado', 'testesteteset', '2025-05-06 02:14:40', '2025-05-07 02:23:33'),
(2, 2, 2, 4, '2025-05-08', '14:30:00', 'cancelado', 'testestetsetsetsetset etsetsetse tset set sets ets etsetsetstset', '2025-05-07 01:30:24', '2025-05-07 02:23:27'),
(3, 2, 2, 5, '2025-05-06', '11:00:00', 'cancelado', '', '2025-05-07 01:51:08', '2025-05-07 03:08:55'),
(4, 2, 2, 3, '2025-05-09', '16:00:00', 'agendado', 'rfyhdfhdfhdfhdfh', '2025-05-07 03:09:36', '2025-05-07 03:09:36'),
(5, 2, 2, 5, '2025-05-07', '16:30:00', 'realizado', 'dsadasdasdasd', '2025-05-07 03:10:12', '2025-05-07 03:10:12'),
(6, 3, 2, 3, '2025-05-16', '16:30:00', 'confirmado', 'dhgsiodhgpsaiodhgp´sdiosd', '2025-05-07 03:14:48', '2025-05-08 01:11:42'),
(7, 2, 3, 3, '2025-05-23', '17:00:00', 'confirmado', '', '2025-05-08 01:56:25', '2025-05-08 01:56:25'),
(8, 3, 3, 3, '2025-05-23', '15:00:00', 'realizado', 'hrfhyrsdfhsdfhd', '2025-05-08 23:46:58', '2025-05-08 23:50:42');

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
(2, 'teste1', 'teste', '33.720.529/0001-39', 'teste2', 'Rua Carlos Alberto Figueredo', '900', '', 'Candeias', 'Vitória da Conquista', 'BA', '45029-268', '7799882930', '77988654101', 'hueltonti@gmail.com', 'https:htecstore.com', '2025-05-01 03:49:10', '2025-05-01 03:49:10', 1),
(3, 'estsetsetst', 'tsetsetsets', '16.205.262/0001-22', 'etssetsetset', 'Rua Carlos Alberto Figueredo', '9000', '', 'Candeias', 'Vitória da Conquista', 'BA', '45029-268', '7798989895', '', '', '', '2025-05-08 01:54:26', '2025-05-08 01:54:26', 1);

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
(3, 'RX', 'Hospita Samur', 0),
(4, 'testes', 'testeste', 0),
(5, 'testetesteste', 'testestset', 1),
(6, 'Ortopedia', 'erstetgststestse', 0),
(7, 'pescoço', 'tsetsetest', 1);

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
(1, 2, 3, NULL, 1),
(2, 2, 5, NULL, 1),
(3, 2, 4, NULL, 1),
(4, 3, 3, NULL, 1),
(5, 3, 7, NULL, 1),
(6, 2, 7, NULL, 1);

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
(1, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-06 23:05:46'),
(2, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-06 23:08:09'),
(3, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=2', 0, NULL, '2025-05-06 23:19:08'),
(4, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-06 23:23:10'),
(5, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=2', 0, NULL, '2025-05-06 23:23:27'),
(6, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=1', 0, NULL, '2025-05-06 23:23:33'),
(7, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-07 00:08:48'),
(8, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-07 00:08:51'),
(9, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-07 00:08:53'),
(10, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-07 00:08:54'),
(11, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Huelton S R Borges foi cancelado', 'index.php?module=agendamentos&action=view&id=3', 0, NULL, '2025-05-07 00:08:55'),
(12, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 09/05/2025 às 16:00:00', 'index.php?module=agendamentos&action=view&id=4', 0, NULL, '2025-05-07 00:09:36'),
(13, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 07/05/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=5', 0, NULL, '2025-05-07 00:10:12'),
(14, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 16/05/2025 às 16:30:00', 'index.php?module=agendamentos&action=view&id=6', 0, NULL, '2025-05-07 00:14:48'),
(15, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Carla Meira dos Santos foi cancelado', 'index.php?module=agendamentos&action=view&id=6', 0, NULL, '2025-05-07 22:11:34'),
(16, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Huelton S R Borges foi criado para 23/05/2025 às 17:00:00', 'index.php?module=agendamentos&action=view&id=7', 0, NULL, '2025-05-07 22:56:25'),
(17, 'agendamento', 'calendar-check', 'primary', 'Novo agendamento criado', 'Agendamento para o paciente Carla Meira dos Santos foi criado para 23/05/2025 às 15:00:00', 'index.php?module=agendamentos&action=view&id=8', 0, NULL, '2025-05-08 20:46:58'),
(18, 'alerta', 'exclamation-triangle', 'warning', 'Agendamento cancelado', 'Agendamento para o paciente Carla Meira dos Santos foi cancelado', 'index.php?module=agendamentos&action=view&id=8', 0, NULL, '2025-05-08 20:50:19');

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
(2, 'Huelton S R Borges', '1992-10-24', '058.155.555-41', '1647474434', 'M', 'Rua Carlos Alberto Figueredo', '900', 'Estrada velha da barra', 'Candeias', 'Vitória da Conquista', 'BA', '45029-268', '', '77999882930', 'hueltonti@gmail.com', '', '', 'Teste de cadastro para pacientes', '2025-05-01 03:27:44', '2025-05-01 03:27:44', 1),
(3, 'Carla Meira dos Santos', '1992-10-24', '125.684.960-02', '1637444456', 'M', 'Rua Carlos Alberto Figueredo', '900', 'teste', 'Candeias', 'Vitória da Conquista', 'BA', '45029-268', '', '77999882931', '', '', '', '', '2025-05-07 03:13:53', '2025-05-07 03:13:53', 1);

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
(5, 'Administrador', 'hueltonti@gmail.com', '$2y$10$c/nR/6KjVlTk1WBAl16oTOmbGBurOGcUAOHwZT9yG3YfthS/5AAua', 'admin', '2025-05-09 01:50:01', 1, '2025-05-08 23:34:39');

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
(1, 3, 'Coluna Lombar', 90.00, 1),
(2, 4, 'tsetes', 90.00, 1),
(3, 3, 'testestestestset', 15.00, 1),
(4, 3, 'terstes', 11.15, 1),
(5, 5, 'testestestests', 805.55, 1),
(6, 6, 'testetestes', 150.02, 1),
(7, 7, 'rx', 2.50, 1);

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
  ADD KEY `especialidade_id` (`especialidade_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `clinicas_parceiras`
--
ALTER TABLE `clinicas_parceiras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `especialidades_clinicas`
--
ALTER TABLE `especialidades_clinicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas_parceiras` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_3` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);

--
-- Limitadores para a tabela `especialidades_clinicas`
--
ALTER TABLE `especialidades_clinicas`
  ADD CONSTRAINT `especialidades_clinicas_ibfk_1` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas_parceiras` (`id`),
  ADD CONSTRAINT `especialidades_clinicas_ibfk_2` FOREIGN KEY (`especialidade_id`) REFERENCES `especialidades` (`id`);

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
