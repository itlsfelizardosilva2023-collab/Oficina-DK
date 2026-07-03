-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27-Jun-2026 às 23:09
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
-- Banco de dados: `oficina_capapelo`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `capital_empresa`
--

CREATE TABLE `capital_empresa` (
  `id_capital` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `fluxo` enum('entrada','saida') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `capital_empresa`
--

INSERT INTO `capital_empresa` (`id_capital`, `descricao`, `id_servico`, `id_contrato`, `fluxo`, `valor`, `data_registro`) VALUES
(1, 'alimentação', NULL, NULL, 'saida', 2000.00, '2026-05-18 22:22:38'),
(2, 'alimentação', NULL, NULL, 'saida', 3000.00, '2026-05-18 22:47:47'),
(3, 'alimentação', NULL, NULL, 'saida', 6000.00, '2026-05-18 23:41:31'),
(4, 'alimentação', NULL, NULL, 'saida', 2000.00, '2026-05-20 20:53:38'),
(5, 'alimentação', NULL, NULL, 'entrada', 1000.00, '2026-05-27 20:26:03'),
(6, 'alimentação', NULL, NULL, 'saida', 1000.00, '2026-05-27 20:26:27'),
(7, 'alimentação', NULL, NULL, 'saida', 6000.00, '2026-06-10 21:06:38'),
(8, 'Pagamento mensal contrato #16 — Maio 2026', NULL, 16, 'entrada', 1764.71, '2026-06-15 13:26:53');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL,
  `nome_cargo` varchar(100) NOT NULL,
  `id_setor` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `nome_cargo`, `id_setor`, `criado_em`) VALUES
(1, 'Mecânico', 1, '2026-06-26 22:53:33'),
(2, 'administrador', 1, '2026-06-26 22:53:33'),
(3, 'ajudante', 2, '2026-06-26 22:53:33'),
(4, 'Recepcionista', 1, '2026-06-27 01:46:30');

-- --------------------------------------------------------

--
-- Estrutura da tabela `carros`
--

CREATE TABLE `carros` (
  `id_carro` int(11) NOT NULL,
  `id_modelo` int(11) NOT NULL,
  `matricula` varchar(20) DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `carros`
--

INSERT INTO `carros` (`id_carro`, `id_modelo`, `matricula`, `cor`, `id_cliente`, `criado_em`) VALUES
(15, 7, 'LD-12-54-PN', 'Vermelho', 13, '2026-04-03 12:03:47'),
(30, 3, 'LD-60-09-PP', 'Amarela', 11, '2026-05-05 19:53:22'),
(31, 7, 'LD-61-19-PB', 'Vermelho', 31, '2026-05-15 23:18:44'),
(32, 6, 'LD-88-72-FD', 'preta', 32, '2026-06-16 22:30:47'),
(33, 10, 'LD-90-09-PN', 'Amarela', 32, '2026-06-16 22:49:14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `numero_bi` varchar(20) NOT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nome`, `telefone`, `numero_bi`, `endereco`, `criado_em`, `email`) VALUES
(6, 'Carla Oliveira', '967890123', '0880087978na90', 'Avenida F, Cazenga', '2026-03-27 15:10:01', ''),
(7, 'Miguel Almeida', '978901234', '0000087978UI90', 'Rua G, Luanda', '2026-03-27 15:10:01', ''),
(8, 'Sofia Rocha', '989012345', '0000087978HU90', 'Avenida H, Luanda', '2026-03-27 15:10:01', ''),
(9, 'Ricardo Lopes', '990123456', '0000087978KU90', 'Rua I, Luanda', '2026-03-27 15:10:01', ''),
(11, 'Felizardo Silva', '943078097', '0000087978MU90', 'Luanda,Benfica,Rua 13', '2026-03-28 01:51:16', ''),
(13, 'Ana Lope', '931696129', '0000087978MA90', 'Luanda/zango-1/capapinha/rua18', '2026-03-28 21:27:33', ''),
(30, 'felizardo silva', '999999999', '0000087978Kk90', 'Luanda/zango-2/capapinha/rua5', '2026-04-09 21:41:28', ''),
(31, 'Lorenço Borges', '926144800', '0000087978BO90', 'Luanda/zango-1/capapinha/rua7', '2026-05-15 23:17:58', ''),
(32, 'joão miguel', '943078097', '0000117978BO90', 'Luanda/cacuaco-2/monana/rua17', '2026-06-10 08:55:17', ''),
(53, 'Cliente Teste Manutencao', '900000001', '111111111LA999', 'Luanda, Talatona', '2026-06-23 09:03:46', 'itlsfelizardosilva2023@gmail.com'),
(54, 'Gemilson Dias', '956504342', '0000087978CU90', 'Luanda/zango-2/capapinha/rua5', '2026-06-24 11:49:21', 'gemilsondias67@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `contratos`
--

CREATE TABLE `contratos` (
  `id_contrato` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `numero_bi` varchar(50) DEFAULT NULL,
  `preco_viatura` decimal(10,2) DEFAULT 0.00,
  `tipo` enum('VIP','Normal') DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `total_geral` decimal(10,2) DEFAULT 0.00,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `numero_transacao` varchar(20) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `status` enum('pago','nao pago') NOT NULL DEFAULT 'pago'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `contratos`
--

INSERT INTO `contratos` (`id_contrato`, `id_cliente`, `numero_bi`, `preco_viatura`, `tipo`, `data_inicio`, `data_fim`, `total_geral`, `criado_em`, `numero_transacao`, `data`, `status`) VALUES
(16, 6, NULL, 0.00, NULL, '2026-03-12', '2027-07-12', 30000.00, '2026-05-15 20:53:12', '7192200', '2026-05-19 00:04:38', 'pago'),
(18, 11, NULL, 0.00, NULL, '2026-03-12', '2027-07-12', 169998.00, '2026-05-15 20:58:25', '7192201', '2026-05-19 00:04:38', 'pago'),
(19, 31, NULL, 0.00, NULL, '2026-05-22', '2027-05-22', 30000.00, '2026-05-15 23:20:24', '8213990', '2026-05-19 00:04:38', 'pago'),
(20, 13, NULL, 0.00, NULL, '2026-05-19', '2027-11-11', 120000.00, '2026-05-17 16:24:41', '71922011', '2026-05-19 00:04:38', 'pago');

-- --------------------------------------------------------

--
-- Estrutura da tabela `contrato_carros`
--

CREATE TABLE `contrato_carros` (
  `id` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `id_carro` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `preco_viatura` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `contrato_carros`
--

INSERT INTO `contrato_carros` (`id`, `id_contrato`, `id_carro`, `criado_em`, `preco_viatura`) VALUES
(13, 18, 30, '2026-05-15 20:58:26', 99998.00),
(14, 19, 31, '2026-05-15 23:20:24', 30000.00),
(16, 20, 15, '2026-05-17 16:24:41', 60000.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `estoque`
--

CREATE TABLE `estoque` (
  `id_estoque` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `tipo` enum('Material','Peça') NOT NULL,
  `nome` varchar(100) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_expiracao` date DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `estoque`
--

INSERT INTO `estoque` (`id_estoque`, `codigo`, `tipo`, `nome`, `marca`, `quantidade`, `data_registo`, `data_expiracao`, `preco`) VALUES
(25, 'MOS01', 'Peça', 'Óleo do Motor', 'Sonangol', 2, '2026-05-21 21:48:58', '2028-08-22', 1000.00),
(26, 'PJH03', 'Peça', 'jante', 'Hyndai', 5, '2026-06-03 21:29:08', '0000-00-00', 5000.00),
(27, '344324', 'Peça', 'luis', 'toyota', 7, '2026-06-17 13:51:07', '3233-02-12', 2000.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `nome_cliente` varchar(255) DEFAULT NULL,
  `placa_carro` varchar(50) DEFAULT NULL,
  `modelo_carro` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `data_factura` datetime DEFAULT current_timestamp(),
  `numero_factura` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `facturas`
--

INSERT INTO `facturas` (`id`, `id_servico`, `nome_cliente`, `placa_carro`, `modelo_carro`, `endereco`, `total`, `data_factura`, `numero_factura`) VALUES
(1, 1, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 20000.00, '2026-05-17 22:23:47', 'FT0001'),
(2, 7, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 6000.00, '2026-05-21 12:50:11', 'FT0002'),
(3, 15, 'Ana Lopes', 'LD-12-54-PN', 'i10', 'Luanda/zango-1/capapinha/rua18', 2000.00, '2026-06-03 22:25:42', 'FT0003'),
(4, 10, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 12500.00, '2026-06-03 22:26:29', 'FT0004'),
(5, 12, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 6840.00, '2026-06-04 14:32:48', 'FT0005'),
(6, 16, 'Sofia Rocha', 'LD 89 67 PN', 'Hilux', 'Avenida H, Luanda', 5000.00, '2026-06-04 14:32:59', 'FT0006'),
(7, 8, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 36024.00, '2026-06-10 22:40:40', 'FT0007'),
(8, 8, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 10800.00, '2026-06-10 22:40:40', 'FT0007'),
(9, 17, 'Sofia Rocha', 'LD-61-19-PB', 'Hilux', 'Luanda,Benfica,Rua 13', 2280.00, '2026-06-22 04:03:51', 'FT0009'),
(10, 9, 'Felizardo Silva', 'LD-60-09-PP', 'Hilux', 'Luanda,Benfica,Rua 13', 91181.76, '2026-06-23 03:35:14', 'FT2026/0010'),
(11, 18, 'Ana Lope', 'LD-12-54-PN', 'i10', 'Oficina', 15960.00, '2026-06-23 11:26:08', 'FT2026/0011'),
(12, 19, 'Lorenço Borges', 'LD-61-19-PB', 'i10', 'Oficina', 13680.00, '2026-06-26 00:11:55', 'FT2026/0012');

-- --------------------------------------------------------

--
-- Estrutura da tabela `factura_itens`
--

CREATE TABLE `factura_itens` (
  `id` int(11) NOT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `codigo_da_peca` varchar(100) DEFAULT NULL,
  `peca_materia` varchar(255) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `factura_itens`
--

INSERT INTO `factura_itens` (`id`, `id_factura`, `codigo_da_peca`, `peca_materia`, `quantidade`, `preco`, `subtotal`) VALUES
(1, 1, '111', 'PECCA', 2, 10000.00, 20000.00),
(7, 2, '1111111', 'PECCA', 6, 1000.00, 6000.00),
(13, 4, '1111111', 'PECCA', 5, 2500.00, 12500.00),
(20, 6, 'PJH03', 'jante', 1, 5000.00, 5000.00),
(21, 6, 'PJH03', 'jante', 1, 5000.00, 5000.00),
(24, 3, 'MOS01', 'Óleo', 2, 1000.00, 2000.00),
(28, 8, '1111111', 'PECCA', 6, 1800.00, 10800.00),
(45, 5, 'MOS01', 'Óleo', 3, 1000.00, 3000.00),
(48, 10, '1111111', 'PECCA', 8, 9998.00, 79984.00),
(62, 9, 'MOS01', 'Óleo do Motor', 1, 1000.00, 1000.00),
(70, 7, '1111111', 'PECCA', 6, 1800.00, 10800.00),
(73, 11, 'MOS01', 'Óleo do Motor', 2, 1000.00, 2000.00),
(75, 12, 'MOS01', 'Óleo do Motor', 1, 1000.00, 1000.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id_funcionario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `endereco` varchar(230) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Activo','Inactivo','Ferias','Suspenso') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id_funcionario`, `nome`, `id_cargo`, `email`, `endereco`, `telefone`, `criado_em`, `status`) VALUES
(21, 'Felizardo  silva', 1, 'itlsilv2023@gmail.com', 'Luanda/zango-3/capapinha/rua5', '931696139', '2026-04-07 13:16:50', 'Activo'),
(22, 'Default Admin', 2, 'DefaultAdmin@gmail.com', 'xxxxxxxxxxxxxxxxxxx', '999999999', '2026-04-07 15:07:03', 'Activo'),
(23, 'Paulo Silva', 1, 'paulosilva2023@gmail.com', 'Luanda/zango-1/capapinha/rua5', '912345678', '2026-04-09 11:56:22', 'Activo'),
(24, 'felizardo silva', 2, 'paulilva2023@gmail.com', 'Luanda/zango-3/capapinha/rua5', '999999999', '2026-04-09 21:19:16', 'Activo'),
(25, 'LADISLAU PEDRO', 3, 'LADISLAU@gmail.com', 'Luanda/zango-3/capapinha/rua5', '954722164', '2026-04-16 10:40:53', 'Activo'),
(27, 'Virgilio Antonio', 1, 'VirgilioAntonio@gmail.com', 'Luanda/zango-3/avenida BK /rua1', '999999999', '2026-05-05 12:17:01', 'Activo'),
(29, 'Carla Oliveira', 4, 'CarlaOliveira@gmail.com', 'Luanda,Benfica,Rua 13', '931696129', '2026-06-27 01:46:31', 'Activo');

-- --------------------------------------------------------

--
-- Estrutura da tabela `lembretes_enviados`
--

CREATE TABLE `lembretes_enviados` (
  `id` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `data_envio` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `lembretes_enviados`
--

INSERT INTO `lembretes_enviados` (`id`, `id_contrato`, `data_envio`) VALUES
(1, 26, '2026-06-22'),
(2, 33, '2026-06-23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `lembretes_manutencao`
--

CREATE TABLE `lembretes_manutencao` (
  `id` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `data_envio` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nome`) VALUES
(3, 'Hyundai'),
(4, 'mitsubishi'),
(2, 'Toyota');

-- --------------------------------------------------------

--
-- Estrutura da tabela `modelos`
--

CREATE TABLE `modelos` (
  `id_modelo` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `id_marca` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `modelos`
--

INSERT INTO `modelos` (`id_modelo`, `nome`, `id_marca`) VALUES
(2, 'Corolla', 2),
(3, 'Hilux', 2),
(5, 'RAV4', 2),
(6, 'Tucson', 3),
(7, 'i10', 3),
(8, 'i10', 3),
(9, 'i10', 2),
(10, 'pajero', 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos_contrato`
--

CREATE TABLE `pagamentos_contrato` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) NOT NULL,
  `mes` tinyint(2) NOT NULL,
  `ano` smallint(4) NOT NULL,
  `num_transacao` varchar(100) NOT NULL,
  `observacao` text DEFAULT NULL,
  `data_pagamento` date NOT NULL,
  `valor` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('pago','pendente','cancelado') NOT NULL DEFAULT 'pago',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recuperacao_conta`
--

CREATE TABLE `recuperacao_conta` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `expiracao` datetime DEFAULT NULL,
  `status` enum('pendente','aprovado','usado') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `recuperacao_conta`
--

INSERT INTO `recuperacao_conta` (`id`, `usuario_id`, `codigo`, `expiracao`, `status`) VALUES
(1, 55, '340716', '2026-04-28 00:55:34', 'aprovado'),
(2, 55, '344324', '2026-04-28 00:51:50', 'usado'),
(3, 55, '669713', '2026-04-28 01:13:06', 'aprovado'),
(4, 55, '569411', '2026-04-28 01:19:58', 'usado'),
(5, 55, '559241', '2026-04-28 01:28:02', 'aprovado'),
(6, 55, '894463', '2026-04-28 01:33:39', 'aprovado'),
(7, 55, '453772', '2026-04-28 01:47:43', 'aprovado'),
(8, 55, '634454', '2026-04-28 01:53:56', 'aprovado'),
(9, 55, '360541', '2026-04-28 01:53:58', 'aprovado'),
(10, 55, '961651', '2026-04-28 13:43:17', 'aprovado'),
(11, 55, '142927', '2026-04-28 13:47:49', 'aprovado'),
(12, 55, '642643', '2026-04-29 01:32:02', 'aprovado'),
(13, 57, '793054', '2026-04-30 01:06:57', 'usado'),
(14, 57, '659653', '2026-05-04 14:49:30', 'aprovado'),
(15, 55, '603160', '2026-05-04 14:51:29', 'aprovado'),
(16, 57, '560100', '2026-05-04 14:52:27', 'aprovado'),
(17, 55, '279342', '2026-05-05 01:34:21', 'aprovado'),
(18, 55, '460192', '2026-05-05 01:37:57', 'aprovado'),
(19, 57, '816054', '2026-05-05 01:38:03', 'usado'),
(20, 55, '183648', '2026-05-05 11:50:07', 'usado'),
(21, 55, '358090', '2026-05-05 14:38:25', 'aprovado'),
(22, 58, '686667', '2026-05-16 02:11:49', 'aprovado'),
(23, 58, '204809', '2026-05-16 02:11:46', 'aprovado'),
(24, 57, '965918', '2026-06-02 13:36:29', 'aprovado'),
(25, 55, '174063', '2026-06-10 23:12:48', 'usado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `placa_carro` varchar(50) DEFAULT NULL,
  `nome_cliente` varchar(100) DEFAULT NULL,
  `modelo_carro` varchar(100) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_mecanico` int(11) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `status` enum('concluido','pendente','andamento','cancelado') NOT NULL DEFAULT 'pendente',
  `id_cliente` int(11) DEFAULT NULL,
  `deslocacao` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cobranca` decimal(10,2) DEFAULT NULL,
  `descricao_servico` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `servicos`
--

INSERT INTO `servicos` (`id`, `placa_carro`, `nome_cliente`, `modelo_carro`, `endereco`, `obs`, `total`, `data_registo`, `id_mecanico`, `data`, `status`, `id_cliente`, `deslocacao`, `cobranca`, `descricao_servico`) VALUES
(9, 'LD-60-09-PP', 'Felizardo Silva', 'Hilux', 'Luanda,Benfica,Rua 13', 'cccccccccccccccccccc', 0.00, '2026-05-18 22:24:21', 25, '2026-05-19 00:03:52', 'andamento', NULL, 0.00, 0.00, ''),
(12, 'LD-60-09-PP', 'Felizardo Silva', 'Hilux', 'Luanda,Benfica,Rua 13', 'XXXXXXXXXX', 3000.00, '2026-05-21 21:51:21', 25, '2026-05-21 22:51:21', 'concluido', NULL, 0.00, NULL, ''),
(17, 'LD-61-19-PB', 'Sofia Rocha', 'Hilux', 'Luanda,Benfica,Rua 13', 'XXXXXXXXXXXXXXXXXX', 7000.00, '2026-06-22 03:03:29', 21, '2026-06-22 04:03:29', 'concluido', NULL, 0.00, 6000.00, ''),
(18, 'LD-12-54-PN', 'Ana Lope', 'i10', 'Oficina', '', 12000.00, '2026-06-23 10:25:50', 21, '2026-06-23 11:25:50', 'concluido', NULL, 0.00, 10000.00, 'revisão'),
(19, 'LD-61-19-PB', 'Lorenço Borges', 'i10', 'Oficina', '', 11000.00, '2026-06-23 21:20:29', 23, '2026-06-23 22:20:29', 'pendente', NULL, 0.00, 10000.00, 'revisão');

-- --------------------------------------------------------

--
-- Estrutura da tabela `servico_itens`
--

CREATE TABLE `servico_itens` (
  `id` int(11) NOT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `codigo_da_peca` varchar(50) DEFAULT NULL,
  `peca_materia` varchar(100) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `servico_itens`
--

INSERT INTO `servico_itens` (`id`, `id_servico`, `codigo_da_peca`, `peca_materia`, `quantidade`, `preco`) VALUES
(9, 9, '1111111', 'PECCA', 8, 9998.00),
(12, 12, 'MOS01', 'Óleo', 3, 1000.00),
(23, 18, 'MOS01', 'Óleo do Motor', 2, 1000.00),
(25, 17, 'MOS01', 'Óleo do Motor', 1, 1000.00),
(26, 19, 'MOS01', 'Óleo do Motor', 1, 1000.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `setores`
--

CREATE TABLE `setores` (
  `id_setor` int(11) NOT NULL,
  `nome_setor` varchar(100) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `setores`
--

INSERT INTO `setores` (`id_setor`, `nome_setor`, `criado_em`) VALUES
(1, 'Mecânica', '2026-06-26 22:53:33'),
(2, 'Funilaria', '2026-06-26 22:53:33');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_funcionario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','usuario','tecnico') NOT NULL DEFAULT 'usuario',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_master_admin` tinyint(1) DEFAULT 0,
  `status` enum('ativo','desativo') NOT NULL DEFAULT 'ativo',
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_funcionario`, `nome`, `email`, `senha`, `nivel`, `criado_em`, `is_master_admin`, `status`, `ativo`) VALUES
(54, 22, 'Default Admin', 'DefaultAdmin@gmail.com', '$2y$10$fMUChTd2qRAlypETBZKFx.lFMAMFulXR96B/ufVGEm9YJWh2bb5P2', 'admin', '2026-04-07 15:07:46', 6, 'ativo', 1),
(55, 23, 'Paulo mama', 'paulosilva2023@gmail.com', '$2y$10$.pAqKtwF/P8TjKTtoGEpRuIdGUVs9dDLCX8L3TKv6xNeVAeuR5wA2', 'tecnico', '2026-04-09 11:57:01', 0, 'ativo', 1),
(57, 21, 'Felizardo  silva', 'itlsilv2023@gmail.com', '$2y$10$VymAhEqPJGGPRcOy8NNZF.97vLTX391tgn7MAfJK15fWGQBEqnH6e', 'usuario', '2026-04-23 10:56:59', 0, 'ativo', 1),
(58, 27, 'Virgilio Antonio', 'VirgilioAntonio@gmail.com', '$2y$10$pKC2bKoqHnhaZF2VNi2wrePdkZZCODyOGh2ykTbOGNwpuzkFkxDGW', 'usuario', '2026-05-05 12:17:32', 0, 'ativo', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_permissoes`
--

CREATE TABLE `usuario_permissoes` (
  `id_permissao` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `pagina` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuario_permissoes`
--

INSERT INTO `usuario_permissoes` (`id_permissao`, `id_usuario`, `pagina`) VALUES
(30, 57, 'clientes.php'),
(29, 57, 'inicio_admin.php'),
(31, 57, 'stok.php');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `capital_empresa`
--
ALTER TABLE `capital_empresa`
  ADD PRIMARY KEY (`id_capital`),
  ADD KEY `fk_servicos` (`id_servico`),
  ADD KEY `fk_contratos` (`id_contrato`);

--
-- Índices para tabela `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`),
  ADD UNIQUE KEY `uniq_cargo_por_setor` (`nome_cargo`,`id_setor`),
  ADD KEY `id_setor` (`id_setor`);

--
-- Índices para tabela `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`id_carro`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `fk_carros_modelo` (`id_modelo`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices para tabela `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`id_contrato`),
  ADD UNIQUE KEY `numero_transacao` (`numero_transacao`),
  ADD KEY `fk_cliente` (`id_cliente`);

--
-- Índices para tabela `contrato_carros`
--
ALTER TABLE `contrato_carros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_carro` (`id_carro`),
  ADD KEY `contrato_carros_ibfk_1` (`id_contrato`);

--
-- Índices para tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id_estoque`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices para tabela `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `factura_itens`
--
ALTER TABLE `factura_itens`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id_funcionario`),
  ADD KEY `fk_funcionario_cargo` (`id_cargo`);

--
-- Índices para tabela `lembretes_enviados`
--
ALTER TABLE `lembretes_enviados`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `lembretes_manutencao`
--
ALTER TABLE `lembretes_manutencao`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices para tabela `modelos`
--
ALTER TABLE `modelos`
  ADD PRIMARY KEY (`id_modelo`),
  ADD KEY `fk_modelos_marca` (`id_marca`);

--
-- Índices para tabela `pagamentos_contrato`
--
ALTER TABLE `pagamentos_contrato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_contrato_mes_ano` (`contrato_id`,`mes`,`ano`);

--
-- Índices para tabela `recuperacao_conta`
--
ALTER TABLE `recuperacao_conta`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `servico_itens`
--
ALTER TABLE `servico_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_servico` (`id_servico`);

--
-- Índices para tabela `setores`
--
ALTER TABLE `setores`
  ADD PRIMARY KEY (`id_setor`),
  ADD UNIQUE KEY `nome_setor` (`nome_setor`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_funcionario` (`id_funcionario`);

--
-- Índices para tabela `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  ADD PRIMARY KEY (`id_permissao`),
  ADD UNIQUE KEY `uq_usuario_pagina` (`id_usuario`,`pagina`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `capital_empresa`
--
ALTER TABLE `capital_empresa`
  MODIFY `id_capital` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `carros`
--
ALTER TABLE `carros`
  MODIFY `id_carro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `contratos`
--
ALTER TABLE `contratos`
  MODIFY `id_contrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `contrato_carros`
--
ALTER TABLE `contrato_carros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id_estoque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `factura_itens`
--
ALTER TABLE `factura_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `lembretes_enviados`
--
ALTER TABLE `lembretes_enviados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `lembretes_manutencao`
--
ALTER TABLE `lembretes_manutencao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id_modelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `pagamentos_contrato`
--
ALTER TABLE `pagamentos_contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recuperacao_conta`
--
ALTER TABLE `recuperacao_conta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `servico_itens`
--
ALTER TABLE `servico_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `setores`
--
ALTER TABLE `setores`
  MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de tabela `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  MODIFY `id_permissao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `capital_empresa`
--
ALTER TABLE `capital_empresa`
  ADD CONSTRAINT `fk_contratos` FOREIGN KEY (`id_contrato`) REFERENCES `contratos` (`id_contrato`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_servicos` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `cargos`
--
ALTER TABLE `cargos`
  ADD CONSTRAINT `cargos_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id_setor`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `carros`
--
ALTER TABLE `carros`
  ADD CONSTRAINT `carros_ibfk_1` FOREIGN KEY (`id_modelo`) REFERENCES `modelos` (`id_modelo`),
  ADD CONSTRAINT `carros_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `fk_carros_modelo` FOREIGN KEY (`id_modelo`) REFERENCES `modelos` (`id_modelo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `fk_contrato_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `contrato_carros`
--
ALTER TABLE `contrato_carros`
  ADD CONSTRAINT `contrato_carros_ibfk_1` FOREIGN KEY (`id_contrato`) REFERENCES `contratos` (`id_contrato`) ON DELETE CASCADE,
  ADD CONSTRAINT `contrato_carros_ibfk_2` FOREIGN KEY (`id_carro`) REFERENCES `carros` (`id_carro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `fk_funcionario_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`);

--
-- Limitadores para a tabela `modelos`
--
ALTER TABLE `modelos`
  ADD CONSTRAINT `fk_modelos_marca` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `modelos_ibfk_1` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `pagamentos_contrato`
--
ALTER TABLE `pagamentos_contrato`
  ADD CONSTRAINT `fk_pag_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id_contrato`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `servico_itens`
--
ALTER TABLE `servico_itens`
  ADD CONSTRAINT `servico_itens_ibfk_1` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id_funcionario`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  ADD CONSTRAINT `usuario_permissoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
