-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 01-Set-2025 às 06:07
-- Versão do servidor: 8.3.0
-- versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `www.fenomenal.comercial.gest`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `endereco` text,
  `user_id` bigint DEFAULT NULL,
  `estado` bigint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `contacto`, `endereco`, `user_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Cliente Exemplo 1', '841234569', 'Av. 1, Maputo', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `entradas`
--

DROP TABLE IF EXISTS `entradas`;
CREATE TABLE IF NOT EXISTS `entradas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `tipo_entrada_id` bigint NOT NULL,
  `fornecedor_id` bigint DEFAULT NULL,
  `fornecedor_ref` varchar(100) DEFAULT NULL,
  `numero_factura` varchar(45) DEFAULT NULL,
  `data_aquisicao` date NOT NULL,
  `data_factura` date NOT NULL,
  `total_factura` decimal(12,2) DEFAULT '0.00',
  `total_desconto` decimal(12,2) DEFAULT '0.00',
  `total_iva` decimal(12,2) DEFAULT '0.00',
  `valor_remanescente` decimal(12,2) DEFAULT '0.00',
  `ficheiro_entrada` text,
  `user_id` bigint DEFAULT NULL,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tipo_entrada_id` (`tipo_entrada_id`),
  KEY `fornecedor_id` (`fornecedor_id`),
  KEY `estado_id` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `entradas`
--

INSERT INTO `entradas` (`id`, `tipo_entrada_id`, `fornecedor_id`, `fornecedor_ref`, `numero_factura`, `data_aquisicao`, `data_factura`, `total_factura`, `total_desconto`, `total_iva`, `valor_remanescente`, `ficheiro_entrada`, `user_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'FENOMENAL COMERCIAL', 'FAC-001/2025', '2025-07-30', '2025-07-30', 5000.00, 0.00, 500.00, 0.00, 'entradas/fac001.pdf', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(2, 1, 1, NULL, '123415', '2025-07-31', '2025-08-20', 25000.00, 45800.00, 5200.00, 6500.00, 'ahhhhhh', 1, 1, '2025-08-29 14:44:08', '2025-08-29 14:44:08'),
(3, 1, 1, NULL, '123415', '2025-07-31', '2025-08-20', 25000.00, 45800.00, 5200.00, 6500.00, 'ahhhhhh', 1, 1, '2025-08-29 14:44:35', '2025-08-29 14:44:35'),
(4, 1, 1, NULL, '123415', '2025-07-31', '2025-08-20', 25000.00, 45800.00, 5200.00, 6500.00, 'ahhhhhh', 1, 1, '2025-08-29 14:46:28', '2025-08-29 14:46:28'),
(5, 1, 1, NULL, '123415', '2025-07-31', '2025-08-20', 25000.00, 45800.00, 5200.00, 6500.00, 'ahhhhhh', 1, 1, '2025-08-29 14:55:34', '2025-08-29 14:55:34'),
(6, 1, 1, NULL, '6545644', '2025-07-29', '2025-08-27', 3500000.00, 0.00, 3000.00, 65000.00, '6544654', 1, 1, '2025-08-29 15:58:22', '2025-08-29 15:58:22'),
(7, 1, 1, NULL, '6545644', '2025-07-29', '2025-08-27', 3500000.00, 0.00, 3000.00, 65000.00, '6544654', 1, 1, '2025-08-29 16:01:31', '2025-08-29 16:01:31'),
(8, 1, 1, NULL, '6545644', '2025-07-29', '2025-08-27', 3500000.00, 0.00, 3000.00, 65000.00, '6544654', 1, 1, '2025-08-29 16:04:21', '2025-08-29 16:04:21'),
(9, 1, 1, NULL, '64565465', '2025-08-20', '2025-08-30', 2500.00, 2000.00, 4000.00, NULL, '564645', 1, 1, '2025-08-29 16:27:06', '2025-08-29 16:27:06'),
(10, 1, 1, NULL, '64565465', '2025-08-20', '2025-08-30', 2500.00, 2000.00, 4000.00, NULL, '564645', 1, 1, '2025-08-29 16:27:36', '2025-08-29 16:27:36'),
(11, 1, 1, NULL, '64565465', '2025-08-20', '2025-08-30', 2500.00, 2000.00, 4000.00, NULL, '564645', 1, 1, '2025-08-29 16:27:49', '2025-08-29 16:27:49'),
(12, 1, 1, NULL, '3211654', '2025-07-29', '2025-08-14', 5000.00, 500.00, 250.00, 6500.00, '1756480759_Imagem WhatsApp 2025-08-28 às 10.48.37_c5bab748.jpg', 1, 1, '2025-08-29 17:19:20', '2025-08-29 17:19:20'),
(13, 1, 1, NULL, '12312', '2025-08-12', '2025-08-31', 450000.00, 0.00, 0.00, 5400.00, '1756637770_Fenomenal DB (2).pdf', 3, 1, '2025-08-31 12:56:10', '2025-08-31 12:56:10'),
(14, 1, 1, NULL, '12312', '2025-08-12', '2025-08-31', 450000.00, 0.00, 0.00, 5400.00, '1756637781_Fenomenal DB (2).pdf', 3, 1, '2025-08-31 12:56:21', '2025-08-31 12:56:21'),
(15, 2, 1, NULL, '53145', '2025-06-11', '2025-08-11', 1000.00, 0.00, 0.00, 1000.00, NULL, 3, 1, '2025-08-31 16:26:34', '2025-08-31 16:26:34'),
(16, 2, 1, NULL, '6545', '2025-08-06', '2027-11-21', 0.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 17:50:27', '2025-08-31 17:50:27'),
(17, 2, 1, NULL, '6545', '2025-08-06', '2027-11-21', 0.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 17:55:09', '2025-08-31 17:55:09'),
(18, 2, 1, NULL, '6545', '2025-08-06', '2027-11-21', 0.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 17:56:05', '2025-08-31 17:56:05'),
(19, 2, 1, NULL, '6545', '2025-08-06', '2027-11-21', 0.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 17:56:19', '2025-08-31 17:56:19'),
(20, 2, 1, NULL, '6545', '2025-08-06', '2027-11-21', 0.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 17:58:43', '2025-08-31 17:58:43'),
(21, 2, 1, NULL, '465454', '2025-02-06', '2025-12-08', 50000.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 17:59:54', '2025-08-31 17:59:54'),
(22, 2, 1, NULL, '465454', '2025-02-06', '2025-12-08', 50000.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 18:05:01', '2025-08-31 18:05:01'),
(23, 2, 1, NULL, '645465', '2025-05-19', '2025-08-31', 0.00, 0.00, 0.00, 0.00, NULL, 3, 1, '2025-08-31 18:06:23', '2025-08-31 18:06:23'),
(24, 2, 1, NULL, '897879', '2025-08-20', '2025-08-31', 0.00, NULL, 0.00, 0.00, NULL, 3, 1, '2025-08-31 18:08:56', '2025-08-31 18:08:56'),
(25, 1, 1, NULL, '5645', '2025-08-14', '2025-08-20', 1000.00, 0.00, 0.00, 1000.00, NULL, 3, 1, '2025-08-31 18:19:31', '2025-08-31 18:19:31'),
(26, 1, 1, NULL, NULL, '2025-08-21', '2025-08-22', 1000.00, NULL, 0.00, 1000.00, NULL, 3, 1, '2025-08-31 18:33:29', '2025-08-31 18:33:29');

-- --------------------------------------------------------

--
-- Estrutura da tabela `entradas_itens`
--

DROP TABLE IF EXISTS `entradas_itens`;
CREATE TABLE IF NOT EXISTS `entradas_itens` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `entrada_id` bigint NOT NULL,
  `produto_id` bigint NOT NULL,
  `codigo_barras_lote` varchar(100) DEFAULT NULL,
  `qtd_caixas` int DEFAULT '0',
  `qtd_por_caixa` int DEFAULT '1',
  `preco_compra_caixa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_compra_unitario` decimal(12,2) DEFAULT NULL,
  `preco_venda_caixa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_venda_unitario` decimal(12,2) DEFAULT NULL,
  `iva` decimal(8,2) DEFAULT '0.00',
  `data_validade` date DEFAULT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`qtd_caixas` * `preco_compra_caixa`)) STORED,
  `user_id` bigint DEFAULT NULL,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `entrada_id` (`entrada_id`),
  KEY `produto_id` (`produto_id`),
  KEY `estado_id` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `entradas_itens`
--

INSERT INTO `entradas_itens` (`id`, `entrada_id`, `produto_id`, `codigo_barras_lote`, `qtd_caixas`, `qtd_por_caixa`, `preco_compra_caixa`, `preco_compra_unitario`, `preco_venda_caixa`, `preco_venda_unitario`, `iva`, `data_validade`, `user_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'PB001-L1', 10, 1, 220.00, 220.00, 300.00, 300.00, 0.00, '2026-07-30', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(2, 1, 2, 'PB002-L1', 5, 1, 185.00, 185.00, 250.00, 250.00, 0.00, '2026-07-30', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(3, 1, 3, 'PB003-L1', 8, 1, 220.00, 220.00, 300.00, 300.00, 0.00, '2026-07-30', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(4, 1, 4, 'PB004-L1', 12, 1, 145.00, 145.00, 200.00, 200.00, 0.00, '2026-07-30', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(5, 1, 5, 'PB005-L1', 2, 1, 800.00, 800.00, 1000.00, 1000.00, 0.00, '2026-07-30', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(6, 1, 6, 'PB006-L1', 3, 1, 450.00, 450.00, 600.00, 600.00, 0.00, '2026-07-30', 1, 1, '2025-08-26 08:49:14', '2025-08-26 08:49:14'),
(7, 11, 4, NULL, 100, 3, 254.00, 150.00, 290.00, 189.00, 0.00, '2027-10-26', 1, 1, '2025-08-29 16:27:49', '2025-08-29 16:27:49'),
(8, 12, 1, NULL, 100, 20, 7500.00, 250.00, 7900.00, 280.00, 0.00, '2026-10-29', 1, 1, '2025-08-29 17:19:20', '2025-08-29 17:19:20'),
(9, 13, 1, NULL, 150, 50, 540.00, 658.00, 510.00, 481.00, 0.00, '2026-05-15', 3, 1, '2025-08-31 12:56:10', '2025-08-31 12:56:10'),
(10, 14, 1, NULL, 150, 50, 540.00, 658.00, 510.00, 481.00, 0.00, '2026-05-15', 3, 1, '2025-08-31 12:56:21', '2025-08-31 12:56:21'),
(11, 14, 2, NULL, 100, 20, 2580.00, 150.00, 3500.00, 290.00, 0.00, '2025-08-31', 3, 1, '2025-08-31 12:56:21', '2025-08-31 12:56:21'),
(12, 15, 5, NULL, 1, 1, 450.00, 250.00, 650.00, 5800.00, 16.00, '2025-08-26', 3, 1, '2025-08-31 16:26:34', '2025-08-31 16:26:34'),
(13, 23, 5, NULL, 400, 1, 0.00, 580.00, 0.00, 680.00, 0.00, '2027-08-05', 3, 1, '2025-08-31 18:06:23', '2025-08-31 18:06:23'),
(14, 24, 1, NULL, 10, 1, 0.00, 150.00, 0.00, 250.00, 0.00, '2025-09-18', 3, 1, '2025-08-31 18:08:56', '2025-08-31 18:08:56'),
(15, 24, 16, NULL, 20, 1, 0.00, 520.00, 0.00, 650.00, 0.00, '2025-10-07', 3, 1, '2025-08-31 18:08:56', '2025-08-31 18:08:56'),
(16, 25, 3, NULL, 10, 1, 0.00, 100.00, 0.00, 150.00, 0.00, '2025-12-16', 3, 1, '2025-08-31 18:19:31', '2025-08-31 18:19:31'),
(17, 26, 2, NULL, 1000, 1, 0.00, 100.00, 0.00, 150.00, 0.00, '2026-06-06', 3, 1, '2025-08-31 18:33:29', '2025-08-31 18:33:29');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE IF NOT EXISTS `estados` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text,
  `estado_id` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `estados`
--

INSERT INTO `estados` (`id`, `nome`, `descricao`, `estado_id`, `created_at`, `updated_at`) VALUES
(1, 'Activo', 'Registo activo no sistema', NULL, '2025-08-03 08:57:12', '2025-08-04 08:57:26'),
(2, 'Inactivo', 'Registo inactivo no sistema', NULL, '2025-08-05 08:57:17', '2025-08-15 08:57:30'),
(3, 'Pendente', 'Registo pendente', NULL, '2025-08-06 08:57:22', '2025-08-11 08:57:33');

-- --------------------------------------------------------

--
-- Estrutura da tabela `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` text,
  `user_id` bigint DEFAULT NULL,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `nome`, `telefone`, `email`, `endereco`, `user_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'FENOMENAL COMERCIAL', '841234567', 'fenomenal@exemplo.com', 'Rua Comercial, Maputo', 1, 1, '2025-08-11 08:58:43', '2025-08-05 08:58:47');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico`
--

DROP TABLE IF EXISTS `historico`;
CREATE TABLE IF NOT EXISTS `historico` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabela` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `row_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `historico`
--

INSERT INTO `historico` (`id`, `descricao`, `tabela`, `row_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Removeu o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2024-09-17 11:05:55', '2024-09-17 11:05:55'),
(2, 'Activou o produto JODEX SPRAY ORAL (IODOPOVIDONA 8 5%) FR. 30ML.', 'produtos', 2, 1, '2024-09-17 11:06:14', '2024-09-17 11:06:14'),
(3, 'Activou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2024-09-17 11:06:40', '2024-09-17 11:06:40'),
(4, 'Removeu o produto DEXA-NEO 0.1% + 0.5% (FOSFATO DE SODIO DE DEXAMETASONA + SULFATO DE NEOMICINA) GOTAS OFTALMICAS FR. 5ML.', 'produtos', 16, 1, '2024-09-17 11:06:51', '2024-09-17 11:06:51'),
(5, 'Removeu o produto ADVIT (VITAMINA A 10000 IU + VITAMINA D 10000 IU)/1ML GOTAS ORAIS FR. 20ML.', 'produtos', 13, 1, '2024-09-17 11:06:55', '2024-09-17 11:06:55'),
(6, 'Removeu o produto ANTIHEMORROIDALE (CLOR. DE EFEDRINA 0.03G + CLOR. DE LIDOCAINA 0.08G + BISMUTO 0.10G + OXIDO DE ZINCO 0.10G + BALSAMO DO PERU 0.10G + OLEO DE RICINO 0.04G) CX. 10 SUPOSITORIOS.', 'produtos', 12, 1, '2024-09-17 11:07:03', '2024-09-17 11:07:03'),
(7, 'Activou o produto ANTIHEMORROIDALE (CLOR. DE EFEDRINA 0.03G + CLOR. DE LIDOCAINA 0.08G + BISMUTO 0.10G + OXIDO DE ZINCO 0.10G + BALSAMO DO PERU 0.10G + OLEO DE RICINO 0.04G) CX. 10 SUPOSITORIOS.', 'produtos', 12, 1, '2024-09-17 11:07:29', '2024-09-17 11:07:29'),
(8, 'Removeu o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2024-09-17 21:37:55', '2024-09-17 21:37:55'),
(9, 'Activou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2024-09-17 21:38:07', '2024-09-17 21:38:07'),
(10, 'Removeu o produto MELIORA (CLORANFENICOL 200MG + METRONIDAZOL 500MG + NISTATINA 660000IU + ACETATO DE HIDROCORTISONA 15MG) CX. 12 OVULOS VAGINAIS.', 'produtos', 5, 1, '2024-09-17 21:56:40', '2024-09-17 21:56:40'),
(11, 'Activou o produto MELIORA (CLORANFENICOL 200MG + METRONIDAZOL 500MG + NISTATINA 660000IU + ACETATO DE HIDROCORTISONA 15MG) CX. 12 OVULOS VAGINAIS.', 'produtos', 5, 1, '2024-09-17 21:57:09', '2024-09-17 21:57:09'),
(12, 'Activou o produto ADVIT (VITAMINA A 10000 IU + VITAMINA D 10000 IU)/1ML GOTAS ORAIS FR. 20ML.', 'produtos', 13, 1, '2024-09-17 22:14:04', '2024-09-17 22:14:04'),
(13, 'Registou o produtoÁgua plus 600 ml.', 'produtos', 22, 2, '2024-09-18 14:48:38', '2024-09-18 14:48:38'),
(14, 'Registou o produtoasfasf.', 'produtos', 23, 2, '2024-09-18 14:52:21', '2024-09-18 14:52:21'),
(15, 'Registou o produtoiohioio.', 'produtos', 24, 2, '2024-09-18 15:01:57', '2024-09-18 15:01:57'),
(16, 'Registou o produtoLabelo (Lips Labial).', 'produtos', 25, 2, '2024-09-18 15:12:39', '2024-09-18 15:12:39'),
(17, 'Removeu o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2024-09-18 17:03:13', '2024-09-18 17:03:13'),
(18, 'Activou o produto DEXA-NEO 0.1% + 0.5% (FOSFATO DE SODIO DE DEXAMETASONA + SULFATO DE NEOMICINA) GOTAS OFTALMICAS FR. 5ML.', 'produtos', 16, 1, '2024-09-18 17:03:27', '2024-09-18 17:03:27'),
(19, 'Removeu o produto Labelo (Lips Labial).', 'produtos', 25, 1, '2024-09-18 17:08:34', '2024-09-18 17:08:34'),
(20, 'Registou o produtoNIVEA MEN 500 ML.', 'produtos', 26, 1, '2024-09-18 17:42:06', '2024-09-18 17:42:06'),
(21, 'Removeu o produto iohioio.', 'produtos', 24, 1, '2024-09-18 17:42:16', '2024-09-18 17:42:16'),
(22, 'Removeu o produto JODEX SPRAY ORAL (IODOPOVIDONA 8 5%) FR. 30ML.', 'produtos', 2, 1, '2024-09-18 17:43:11', '2024-09-18 17:43:11'),
(23, 'Activou o produto Labelo (Lips Labial).', 'produtos', 25, 1, '2024-09-18 17:44:35', '2024-09-18 17:44:35'),
(24, 'Removeu o produto METRONIDAZOL 250MG CX. 30COMP..', 'produtos', 3, 1, '2024-09-18 17:46:44', '2024-09-18 17:46:44'),
(25, 'Registou o utilizador Adelson Saguate.', 'users', 3, 1, '2024-09-19 13:35:21', '2024-09-19 13:35:21'),
(26, 'Registou o utilizador Testador.', 'users', 4, 2, '2024-09-19 13:40:15', '2024-09-19 13:40:15'),
(27, 'Removeu o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 2, '2024-09-19 14:38:46', '2024-09-19 14:38:46'),
(28, 'Removeu o funcionario Administrador.', 'users', 1, 2, '2024-09-19 14:50:33', '2024-09-19 14:50:33'),
(29, 'Activou o funcionario Administrador.', 'users', 1, 2, '2024-09-19 14:51:18', '2024-09-19 14:51:18'),
(30, 'Registou o produtoCimento Leão.', 'produtos', 27, 2, '2024-09-19 14:56:27', '2024-09-19 14:56:27'),
(31, 'Registou o produto jhkjhvngbv.', 'produtos', 28, 2, '2024-09-19 15:06:02', '2024-09-19 15:06:02'),
(32, 'Registou o produto jjjjjjjjjjjjjj.', 'produtos', 29, 2, '2024-09-19 15:12:18', '2024-09-19 15:12:18'),
(33, 'Actualizou o produto DOXYDERMA (DOXICICLINA MONOHIDRATADA) 100MG CX. 20COMP.', 'produtos', 4, 2, '2024-09-19 17:22:35', '2024-09-19 17:22:35'),
(34, 'Actualizou o produto DOXYDERMA (DOXICICLINA MONOHIDRATADA) 100MG CX. 20COMP.', 'produtos', 4, 2, '2024-09-19 17:23:37', '2024-09-19 17:23:37'),
(35, 'Actualizou o produto DOXYDERMA (DOXICICLINA MONOHIDRATADA) 100MG CX. 20COMP.', 'produtos', 4, 1, '2024-09-19 17:24:31', '2024-09-19 17:24:31'),
(36, 'Actualizou o funcionario Administrador', 'users', 1, 1, '2024-09-20 07:31:13', '2024-09-20 07:31:13'),
(37, 'Actualizou o funcionario Adelson Saguate', 'users', 3, 1, '2024-09-20 07:31:49', '2024-09-20 07:31:49'),
(38, 'Registou o produto adadad.', 'produtos', 30, 1, '2024-09-20 08:15:35', '2024-09-20 08:15:35'),
(39, 'Actualizou o produto adadad', 'produtos', 30, 1, '2024-09-20 08:16:16', '2024-09-20 08:16:16'),
(40, 'Actualizou o produto adadad', 'produtos', 30, 1, '2024-09-20 08:17:26', '2024-09-20 08:17:26'),
(41, 'Actualizou o produto adadad', 'produtos', 30, 1, '2024-09-20 08:18:16', '2024-09-20 08:18:16'),
(42, 'Actualizou o produto adadad', 'produtos', 30, 1, '2024-09-20 08:19:36', '2024-09-20 08:19:36'),
(43, 'Actualizou o produto adadad', 'produtos', 30, 1, '2024-09-20 08:34:10', '2024-09-20 08:34:10'),
(44, 'Actualizou o produto jhkjhvngbv', 'produtos', 28, 1, '2024-09-20 08:34:53', '2024-09-20 08:34:53'),
(45, 'Actualizou o produto jhkjhvngbv', 'produtos', 28, 1, '2024-09-20 08:48:59', '2024-09-20 08:48:59'),
(46, 'Actualizou o produto Arroz leão 50 Kg', 'produtos', 28, 1, '2024-09-20 08:51:26', '2024-09-20 08:51:26'),
(47, 'Actualizou o produto Varrão nr 8', 'produtos', 29, 1, '2024-09-20 08:52:08', '2024-09-20 08:52:08'),
(48, 'Actualizou o produto Oleo Maeva 300 ml', 'produtos', 30, 1, '2024-09-20 08:52:29', '2024-09-20 08:52:29'),
(49, 'Removeu o funcionario Administrador.', 'users', 1, 2, '2024-09-20 14:43:13', '2024-09-20 14:43:13'),
(50, 'Activou o funcionario Administrador.', 'users', 1, 2, '2024-09-20 14:43:22', '2024-09-20 14:43:22'),
(51, 'Registou a requisição 4.', 'requisicoes', 4, 2, '2024-09-20 16:02:42', '2024-09-20 16:02:42'),
(52, 'Registou a requisição 5.', 'requisicoes', 5, 2, '2024-09-20 16:04:05', '2024-09-20 16:04:05'),
(53, 'Registou a requisição 6.', 'requisicoes', 6, 2, '2024-09-20 16:07:01', '2024-09-20 16:07:01'),
(54, 'Registou a requisição 7.', 'requisicoes', 7, 2, '2024-09-20 16:08:17', '2024-09-20 16:08:17'),
(55, 'Registou a requisição 8.', 'requisicoes', 8, 2, '2024-09-20 16:09:23', '2024-09-20 16:09:23'),
(56, 'Registou a requisição 9.', 'requisicoes', 9, 2, '2024-09-20 16:10:07', '2024-09-20 16:10:07'),
(57, 'Registou a requisição 10.', 'requisicoes', 10, 2, '2024-09-20 16:10:18', '2024-09-20 16:10:18'),
(58, 'Registou a requisição 11.', 'requisicoes', 11, 2, '2024-09-20 16:10:27', '2024-09-20 16:10:27'),
(59, 'Activou o produto JODEX SPRAY ORAL (IODOPOVIDONA 8 5%) FR. 30ML.', 'produtos', 2, 2, '2024-09-20 17:09:46', '2024-09-20 17:09:46'),
(60, 'Activou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 2, '2024-09-20 17:09:50', '2024-09-20 17:09:50'),
(61, 'Activou o produto METRONIDAZOL 250MG CX. 30COMP..', 'produtos', 3, 2, '2024-09-20 17:09:55', '2024-09-20 17:09:55'),
(62, 'Activou o produto iohioio.', 'produtos', 24, 2, '2024-09-20 17:09:59', '2024-09-20 17:09:59'),
(63, 'Actualizou o produto NIVEA MEN 500 ML', 'produtos', 26, 3, '2024-09-20 17:11:52', '2024-09-20 17:11:52'),
(64, 'Registou a requisição 12.', 'requisicoes', 12, 3, '2024-09-20 17:12:12', '2024-09-20 17:12:12'),
(65, 'Registou a requisição 13.', 'requisicoes', 13, 1, '2024-09-20 19:42:52', '2024-09-20 19:42:52'),
(66, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 1, '2024-09-20 19:43:51', '2024-09-20 19:43:51'),
(67, 'Registou o produto asdasdada.', 'produtos', 31, 1, '2024-09-21 12:36:14', '2024-09-21 12:36:14'),
(68, 'Registou a requisição 14.', 'requisicoes', 14, 1, '2024-09-21 12:39:38', '2024-09-21 12:39:38'),
(69, 'Registou o produto Cimento Dugongo.', 'produtos', 32, 1, '2024-09-21 12:40:10', '2024-09-21 12:40:10'),
(70, 'Registou a requisição 15.', 'requisicoes', 15, 1, '2024-09-21 12:42:16', '2024-09-21 12:42:16'),
(71, 'Actualizou o produto OLa', 'produtos', 1, 3, '2024-09-21 12:50:56', '2024-09-21 12:50:56'),
(72, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 3, '2024-09-21 12:51:04', '2024-09-21 12:51:04'),
(73, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 3, '2024-09-21 12:51:13', '2024-09-21 12:51:13'),
(74, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 3, '2024-09-21 12:51:23', '2024-09-21 12:51:23'),
(75, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 3, '2024-09-21 12:51:31', '2024-09-21 12:51:31'),
(76, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 3, '2024-09-21 12:51:44', '2024-09-21 12:51:44'),
(77, 'Registou a requisição 16.', 'requisicoes', 16, 1, '2024-09-28 14:45:30', '2024-09-28 14:45:30'),
(78, 'Actualizou o produto LOSARTAN (LOSARTAN DE POTASSIO) 50MG CX. 30COMP.', 'produtos', 7, 1, '2024-09-28 14:46:41', '2024-09-28 14:46:41'),
(79, 'Registou a requisição 17.', 'requisicoes', 17, 1, '2024-09-28 16:27:57', '2024-09-28 16:27:57'),
(80, 'Registou a requisição 18.', 'requisicoes', 18, 1, '2024-09-28 16:28:07', '2024-09-28 16:28:07'),
(81, 'Registou a requisição 19.', 'requisicoes', 19, 1, '2024-09-28 16:28:22', '2024-09-28 16:28:22'),
(82, 'Registou a requisição 20.', 'requisicoes', 20, 1, '2024-09-28 16:28:31', '2024-09-28 16:28:31'),
(83, 'Registou a requisição 21.', 'requisicoes', 21, 1, '2024-09-30 16:14:49', '2024-09-30 16:14:49'),
(84, 'Registou a requisição 22.', 'requisicoes', 22, 1, '2024-09-30 16:23:23', '2024-09-30 16:23:23'),
(85, 'Registou a requisição 23.', 'requisicoes', 23, 1, '2024-12-27 16:10:43', '2024-12-27 16:10:43'),
(86, 'Removeu o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2025-02-13 21:51:05', '2025-02-13 21:51:05'),
(87, 'Registou o produto Ferro para construção de edificios  e outros.', 'produtos', 33, 1, '2025-04-13 11:41:06', '2025-04-13 11:41:06'),
(88, 'Actualizou o produto Tecido de Cetin', 'produtos', 24, 3, '2025-06-14 13:09:22', '2025-06-14 13:09:22'),
(89, 'Registou a requisição 24.', 'requisicoes', 24, 3, '2025-06-14 13:09:45', '2025-06-14 13:09:45'),
(90, 'Actualizou o produto Tecido de Cetin', 'produtos', 24, 1, '2025-06-14 13:13:11', '2025-06-14 13:13:11'),
(91, 'Registou o utilizador Isabel Guivalar.', 'users', 6, 1, '2025-08-18 20:34:10', '2025-08-18 20:34:10'),
(92, 'Actualizou o funcionario Isabel Guivalar', 'users', 6, 6, '2025-08-18 20:38:01', '2025-08-18 20:38:01'),
(93, 'Activou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP..', 'produtos', 1, 1, '2025-08-20 21:08:15', '2025-08-20 21:08:15'),
(94, 'Registou a requisição 25.', 'requisicoes', 25, 1, '2025-08-22 10:26:04', '2025-08-22 10:26:04'),
(95, 'Actualizou o produto Agrafo 80/12', 'produtos', 12, 1, '2025-08-26 11:46:24', '2025-08-26 11:46:24'),
(96, 'Registou o produto teste.', 'produtos', 33, 1, '2025-08-28 09:42:27', '2025-08-28 09:42:27'),
(97, 'Registou o produto Água Namaacha 500 ML.', 'produtos', 34, 1, '2025-08-28 10:42:34', '2025-08-28 10:42:34'),
(98, 'Registou o produto acqcqqc.', 'produtos', 35, 1, '2025-08-28 10:44:49', '2025-08-28 10:44:49'),
(99, 'Registou o produto Cola de Sapatos 4l Azever.', 'produtos', 36, 1, '2025-08-28 10:45:30', '2025-08-28 10:45:30'),
(100, 'Registou o produto Tecido de Linho Castanho.', 'produtos', 37, 1, '2025-08-28 15:08:22', '2025-08-28 15:08:22'),
(101, 'Registou o produto Tecido de Linho Castanho.', 'produtos', 38, 1, '2025-08-28 15:15:44', '2025-08-28 15:15:44'),
(102, 'Registou o produto Tecido de Linho Castanho.', 'produtos', 39, 1, '2025-08-28 15:21:28', '2025-08-28 15:21:28'),
(103, 'Registou o produto Tecido de Lingo Creme.', 'produtos', 40, 1, '2025-08-28 15:22:52', '2025-08-28 15:22:52'),
(104, 'Registou a entrada Nº 11 com 1 itens.', 'entradas', 11, 1, '2025-08-29 16:27:49', '2025-08-29 16:27:49'),
(105, 'Registou a entrada Nº 12 com 1 itens.', 'entradas', 12, 1, '2025-08-29 17:19:20', '2025-08-29 17:19:20'),
(106, 'Registou a entrada Nº 14 com 2 itens.', 'entradas', 14, 3, '2025-08-31 12:56:21', '2025-08-31 12:56:21'),
(107, 'Registou a entrada Nº 15 com 1 itens.', 'entradas', 15, 3, '2025-08-31 16:26:34', '2025-08-31 16:26:34'),
(108, 'Registou a entrada Nº 23 com 1 itens.', 'entradas', 23, 3, '2025-08-31 18:06:23', '2025-08-31 18:06:23'),
(109, 'Registou a entrada Nº 24 com 2 itens.', 'entradas', 24, 3, '2025-08-31 18:08:56', '2025-08-31 18:08:56'),
(110, 'Registou a entrada Nº 25 com 1 itens.', 'entradas', 25, 3, '2025-08-31 18:19:31', '2025-08-31 18:19:31'),
(111, 'Registou a entrada Nº 26 com 1 itens.', 'entradas', 26, 3, '2025-08-31 18:33:29', '2025-08-31 18:33:29'),
(112, 'Registou a saída Nº VD 2/2025 com 1 itens.', 'saidas', 3, 3, '2025-08-31 22:50:10', '2025-08-31 22:50:10'),
(113, 'Registou a saída Nº VD 3/2025 com 2 itens.', 'saidas', 6, 3, '2025-08-31 23:58:01', '2025-08-31 23:58:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2020_05_13_093539_create_tipo_proviniencias_table', 1),
(4, '2020_05_13_113137_create_estados_table', 1),
(5, '2020_05_13_113245_create_users_table', 1),
(6, '2020_05_13_113255_create_tipo_entradas_table', 1),
(7, '2020_05_13_113303_create_tipo_saidas_table', 1),
(8, '2020_05_13_113316_create_tipo_produtos_table', 1),
(9, '2020_05_13_113317_create_tipo_pagamentos_table', 1),
(10, '2020_05_13_113330_create_clientes_table', 1),
(11, '2020_05_13_113354_create_fornecedors_table', 1),
(12, '2020_05_13_113431_create_produtos_table', 1),
(13, '2020_05_13_113441_create_lotes_table', 1),
(14, '2020_05_13_113449_create_saidas_table', 1),
(15, '2020_05_13_113457_create_saida_items_table', 1),
(16, '2020_05_26_155618_create_forma_pagamentos_table', 1),
(17, '2020_06_06_105127_create_permissaos_table', 1),
(18, '2020_06_06_114529_create_permissao_user_table', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `numeracao`
--

DROP TABLE IF EXISTS `numeracao`;
CREATE TABLE IF NOT EXISTS `numeracao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL,
  `ano` year NOT NULL,
  `tipo` enum('venda_dinheiro','venda_credito','cotacao','fornecedor','nota_credito_fornecedor','inventario','devolucao','nota_credito','abate','cliente','sessao') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tipo` (`tipo`),
  KEY `ano` (`ano`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `numeracao`
--

INSERT INTO `numeracao` (`id`, `numero`, `ano`, `tipo`, `created_at`, `updated_at`) VALUES
(1, 8, '2025', 'fornecedor', '2025-03-27 09:41:57', '2025-08-31 14:21:06'),
(2, 17, '2025', 'venda_credito', '2025-03-28 09:18:12', '2025-04-01 08:40:35'),
(3, 3, '2025', 'venda_dinheiro', '2025-08-31 16:04:34', '2025-08-31 23:58:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissaos`
--

DROP TABLE IF EXISTS `permissaos`;
CREATE TABLE IF NOT EXISTS `permissaos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `permissaos`
--

INSERT INTO `permissaos` (`id`, `nome`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2020-08-19 12:27:00', '2020-08-19 12:27:00'),
(2, 'gestor', '2020-08-19 12:27:00', '2020-08-19 12:27:00'),
(3, 'funcionario_normal', '2020-08-19 12:27:01', '2020-08-19 12:27:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissao_user`
--

DROP TABLE IF EXISTS `permissao_user`;
CREATE TABLE IF NOT EXISTS `permissao_user` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permissao_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `permissao_user`
--

INSERT INTO `permissao_user` (`id`, `permissao_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-09-17 10:43:44', '2024-09-17 10:43:44'),
(2, 2, 2, '2024-09-17 10:43:44', '2024-09-17 10:43:44'),
(3, 3, 3, '2024-09-17 10:43:44', '2024-09-17 10:43:44'),
(5, 3, 6, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text,
  `stock_minimo` int DEFAULT '0',
  `imagem` varchar(255) DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `unidade_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estado_id` (`estado`),
  KEY `fk_produtos_unidades` (`unidade_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `codigo_barras`, `nome`, `descricao`, `stock_minimo`, `imagem`, `user_id`, `estado`, `created_at`, `updated_at`, `unidade_id`) VALUES
(1, 'PB001', 'Napa', 'Napa', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(2, 'PB002', 'Veludo HL001', 'Veludo HL001', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(3, 'PB003', 'Linho', 'Linho', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(4, 'PB004', 'Linho RE', 'Linho RE', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(5, 'PB005', 'Molas grandes', 'Molas grandes', -10, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(6, 'PB006', 'Molas pequenas', 'Molas pequenas', -100, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(7, 'PB007', 'Mola Quadrada', 'Mola Quadrada', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(8, 'PB008', 'Prega mola', 'Prega mola', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(9, 'PB009', 'Cinta elástica grande', 'Cinta elástica grande', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(10, 'PB010', 'Cinta elástica pequena', 'Cinta elástica pequena', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(11, 'PB011', 'Agrafo 10/13', 'Agrafo 10/13', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 4),
(12, 'PB012', 'Agrafo 80/12', 'Agrafo 80/12', 25, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 11:46:24', 4),
(13, 'PB013', 'Caixinha de Agrafo montagem N-21', 'Caixinha de Agrafo montagem N-21', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 4),
(14, 'PB014', 'Encaixo', 'Encaixo', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(15, 'PB015', 'Cola (contato, Spray) 1L', 'Cola 1L', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 5),
(16, 'PB016', 'Dacron', 'Dacron', -5, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(17, 'PB017', 'Rolo Dacron', 'Rolo Dacron', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 6),
(18, 'PB018', 'Botões diamante', 'Botões diamante', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(19, 'PB019', 'Calico', 'Calico', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 2),
(20, 'PB020', 'Pernas Plásticas', 'Pernas Plásticas', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(21, 'PB021', 'Aderente pequeno', 'Aderente pequeno', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(22, 'PB022', 'Aderente Grande', 'Aderente Grande', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(23, 'PB023', 'Saco', 'Saco', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 7),
(24, 'PB024', 'Prego', 'Prego', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(25, 'PB025', 'Copo', 'Copo', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(26, 'PB026', 'Pionizes', 'Pionizes', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 1),
(27, 'PB027', 'Placa branca 1 cm', 'Placa branca 1 cm', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 3),
(28, 'PB028', 'Placa Azul', 'Placa Azul', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 3),
(29, 'PB029', 'Placa Cinzenta 5cm x 75', 'Placa Cinzenta 5cm x 75', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 3),
(30, 'PB030', 'Enchimento', 'Enchimento', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 7),
(31, 'PB031', 'Flocos', 'Flocos', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 7),
(32, 'PB032', 'Placa 10 cm', 'Placa 10 cm', 0, NULL, 2, 1, '2025-08-26 09:18:26', '2025-08-26 09:18:26', 3),
(40, '4564654', 'Tecido de Linho', 'Tecido de Lingo Creme', 150, 'Captura de ecrã 2025-08-28 150708_1756387372.png', 1, 1, '2025-08-28 15:22:52', '2025-08-28 15:22:52', 1),
(34, 'PB994', 'Agua Namacha', 'Água Namaacha 500 ML', 150, NULL, 1, 1, '2025-08-28 10:42:34', '2025-08-28 10:42:34', NULL),
(39, '946546532', 'Tecido de Linho', 'Tecido de Linho Castanho', 45, 'Outubro_Rosa_1756387288.png', 1, 1, '2025-08-28 15:21:28', '2025-08-28 15:21:28', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `requisicoes`
--

DROP TABLE IF EXISTS `requisicoes`;
CREATE TABLE IF NOT EXISTS `requisicoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `produto_id` int DEFAULT NULL,
  `quantidade` float DEFAULT NULL,
  `estado_requisicao` enum('1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '1 => PENDENTE, 2 => APROVADA, 3 => REPROVADA',
  `estado` enum('1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `saidas_user_id_foreign` (`user_id`),
  KEY `activo` (`estado`),
  KEY `estado_pagamento` (`estado_requisicao`),
  KEY `idx_saidas_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `requisicoes`
--

INSERT INTO `requisicoes` (`id`, `produto_id`, `quantidade`, `estado_requisicao`, `estado`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 20, '2', '1', 2, '2024-09-20 10:03:24', '2024-09-20 14:33:20'),
(2, 4, 30, '2', '1', 2, '2024-09-20 13:50:09', '2024-09-20 14:59:26'),
(3, 4, 30, '2', '1', 2, '2024-09-20 13:50:18', '2024-09-20 15:00:43'),
(4, 2, 50, '2', '1', 2, '2024-09-20 14:02:42', '2024-09-20 15:10:21'),
(5, 3, 10, '2', '1', 2, '2024-09-20 14:04:05', '2024-09-20 15:02:16'),
(6, 8, 40, '2', '1', 2, '2024-09-20 14:07:01', '2024-09-28 12:39:40'),
(7, 12, 10, '3', '1', 2, '2024-06-11 14:08:17', '2024-09-20 18:33:18'),
(8, 13, 12, '3', '1', 2, '2024-08-07 14:09:23', '2024-09-20 18:32:07'),
(9, 6, 10, '2', '1', 2, '2024-09-20 14:10:07', '2024-09-20 17:39:47'),
(10, 19, 5, '3', '1', 2, '2024-09-20 14:10:18', '2024-09-20 18:31:48'),
(11, 27, 60, '1', '1', 2, '2024-09-20 14:10:27', '2024-09-20 14:10:27'),
(12, 26, 200, '3', '1', 3, '2024-09-20 15:12:12', '2024-09-20 15:13:16'),
(13, 3, 30, '2', '1', 1, '2024-09-20 17:42:52', '2024-09-20 17:43:06'),
(14, 1, 80, '3', '1', 1, '2024-09-21 10:39:38', '2024-09-28 12:39:52'),
(15, 7, 60, '2', '1', 1, '2024-09-21 10:42:16', '2024-09-28 12:47:02'),
(16, 7, 20, '2', '1', 1, '2024-09-28 12:45:30', '2024-09-28 12:47:08'),
(17, 3, 20, '2', '1', 1, '2024-09-28 14:27:57', '2024-10-18 18:01:59'),
(18, 4, 100, '2', '1', 1, '2024-09-28 14:28:07', '2025-02-13 19:52:04'),
(19, 13, 50, '3', '1', 1, '2024-09-28 14:28:22', '2025-02-13 19:52:08'),
(20, 6, 300, '3', '1', 1, '2024-09-28 14:28:31', '2025-08-20 19:10:00'),
(21, 1, 100, '3', '1', 1, '2024-09-30 14:14:49', '2024-09-30 14:22:52'),
(22, 9, 100, '1', '1', 1, '2024-09-30 14:23:23', '2024-09-30 14:23:23'),
(23, 1, 100, '2', '1', 1, '2024-12-27 14:10:43', '2024-12-27 14:11:40'),
(24, 24, 100, '2', '1', 3, '2025-06-14 11:09:45', '2025-06-14 11:13:46'),
(25, 1, 20, '1', '1', 1, '2025-08-22 08:26:04', '2025-08-22 08:26:04');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saidas`
--

DROP TABLE IF EXISTS `saidas`;
CREATE TABLE IF NOT EXISTS `saidas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint UNSIGNED DEFAULT NULL,
  `tipo_saida_id` bigint UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `valor_total` decimal(12,2) DEFAULT NULL,
  `valor_total_iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `valor_pago` decimal(12,2) DEFAULT NULL,
  `valor_remanescente` decimal(12,2) DEFAULT NULL,
  `desconto` decimal(12,2) DEFAULT NULL,
  `valor_entregue` decimal(12,2) DEFAULT NULL,
  `trocos` decimal(12,2) DEFAULT NULL,
  `tipo_pagamento_id` bigint UNSIGNED DEFAULT NULL,
  `numero` int DEFAULT NULL,
  `numero_cotacao` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validade_cotacao` date DEFAULT NULL,
  `slip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_factura` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_pagamento` enum('pago','nao_pago','parcial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pago',
  `activo` enum('1','0','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1 => activo, 0 => eliminado, 2 => ..., 3 => devolvida',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `saidas_user_id_foreign` (`user_id`),
  KEY `saidas_cliente_id_foreign` (`cliente_id`),
  KEY `saidas_tipo_saida_id_foreign` (`tipo_saida_id`),
  KEY `saidas_tipo_pagamento_id_foreign` (`tipo_pagamento_id`),
  KEY `idx_data` (`data`),
  KEY `idx_activo` (`activo`),
  KEY `idx_estado_pagamento` (`estado_pagamento`),
  KEY `idx_saidas_id` (`id`),
  KEY `idx_saidas_tipo_saida_id` (`tipo_saida_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `saidas`
--

INSERT INTO `saidas` (`id`, `cliente_id`, `tipo_saida_id`, `data`, `valor_total`, `valor_total_iva`, `valor_pago`, `valor_remanescente`, `desconto`, `valor_entregue`, `trocos`, `tipo_pagamento_id`, `numero`, `numero_cotacao`, `validade_cotacao`, `slip`, `numero_factura`, `estado_pagamento`, `activo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-08-31', 1500.00, 240.00, 1000.00, 500.00, 0.00, 1000.00, 0.00, 1, 123, 'COT-2025-001', '2025-09-30', 'slip123.pdf', 'FACT-2025-001', 'parcial', '1', 1, '2025-08-31 18:33:42', '2025-08-31 18:33:42'),
(3, 1, 1, '2025-08-31', 1500.00, 0.00, NULL, 1500.00, 0.00, 1000.00, NULL, 1, NULL, NULL, NULL, NULL, 'VD 2/2025', 'pago', '1', 3, '2025-08-31 20:50:10', '2025-08-31 20:50:10'),
(6, 1, 1, '2025-08-31', 63250.00, 0.00, 63250.00, 0.00, 0.00, 63250.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'VD 3/2025', 'pago', '1', 3, '2025-08-31 21:58:01', '2025-08-31 21:58:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saida_itens`
--

DROP TABLE IF EXISTS `saida_itens`;
CREATE TABLE IF NOT EXISTS `saida_itens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `saida_id` bigint UNSIGNED NOT NULL,
  `produto_id` bigint UNSIGNED NOT NULL,
  `entrada_item_id` bigint UNSIGNED DEFAULT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `preco_unitario` decimal(12,2) NOT NULL,
  `preco_compra` decimal(12,2) NOT NULL,
  `iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `valor_iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `custo` decimal(12,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL,
  `desconto_valor` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tipo_motivo` int DEFAULT NULL COMMENT '2=>fora_do_prazo, 5=>danificado, 1=>saida_armazem, 4=>oferta, 3=>outro',
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `activo` enum('1','0','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `saida_itens_user_id_foreign` (`user_id`),
  KEY `saida_itens_saida_id_foreign` (`saida_id`),
  KEY `saida_itens_entrada_item_id_foreign` (`entrada_item_id`),
  KEY `idx_produto_id` (`produto_id`),
  KEY `idx_activo` (`activo`),
  KEY `idx_tipo_motivo` (`tipo_motivo`),
  KEY `idx_saida_itens_saida_id` (`saida_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `saida_itens`
--

INSERT INTO `saida_itens` (`id`, `saida_id`, `produto_id`, `entrada_item_id`, `quantidade`, `preco_unitario`, `preco_compra`, `iva`, `valor_iva`, `custo`, `desconto_percentual`, `desconto_valor`, `tipo_motivo`, `motivo`, `user_id`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10, 5.000, 200.00, 150.00, 16.00, 80.00, 750.00, 0.00, 0.00, 1, 'Venda normal', 1, '1', '2025-08-31 18:34:06', '2025-08-31 18:34:06'),
(2, 3, 5, NULL, 10.000, 150.00, 350.00, 0.00, 0.00, 1500.00, 0.00, 0.00, 1, 'kjhkk', 3, '1', '2025-08-31 20:50:10', '2025-08-31 20:50:10'),
(3, 6, 6, NULL, 100.000, 600.00, 600.00, 0.00, 0.00, 60000.00, 0.00, 0.00, NULL, NULL, 3, '1', '2025-08-31 21:58:01', '2025-08-31 21:58:01'),
(4, 6, 16, NULL, 5.000, 650.00, 650.00, 0.00, 0.00, 3250.00, 0.00, 0.00, NULL, NULL, 3, '1', '2025-08-31 21:58:01', '2025-08-31 21:58:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos_entradas`
--

DROP TABLE IF EXISTS `tipos_entradas`;
CREATE TABLE IF NOT EXISTS `tipos_entradas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `tipos_entradas`
--

INSERT INTO `tipos_entradas` (`id`, `nome`, `descricao`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Compra', 'Entrada de produtos via compra', 1, NULL, NULL),
(2, 'Transferência', 'Entrada de produtos via transferência', 1, NULL, NULL),
(3, 'Inventário', 'Entrada via ajuste de inventário', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos_saidas`
--

DROP TABLE IF EXISTS `tipos_saidas`;
CREATE TABLE IF NOT EXISTS `tipos_saidas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `tipos_saidas`
--

INSERT INTO `tipos_saidas` (`id`, `nome`, `descricao`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Venda Normal', 'Venda direta a clientes', 1, NULL, NULL),
(2, 'Venda a Crédito', 'Venda com pagamento posterior', 1, NULL, NULL),
(3, 'Transferência', 'Saída via transferência', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_motivo`
--

DROP TABLE IF EXISTS `tipo_motivo`;
CREATE TABLE IF NOT EXISTS `tipo_motivo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipo_motivo`
--

INSERT INTO `tipo_motivo` (`id`, `descricao`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Transferência', '1', '2024-05-02 13:32:57', '2024-05-02 13:32:57'),
(2, 'Fora do prazo', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02'),
(3, 'Outros', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02'),
(4, 'Oferta', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02'),
(5, 'Danificado', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_pagamentos`
--

DROP TABLE IF EXISTS `tipo_pagamentos`;
CREATE TABLE IF NOT EXISTS `tipo_pagamentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `designacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_pagamentos_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tipo_pagamentos`
--

INSERT INTO `tipo_pagamentos` (`id`, `designacao`, `user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Numerário', NULL, '1', '2020-08-19 12:27:02', '2020-08-19 12:27:02'),
(2, 'MPesa', NULL, '1', '2020-08-19 12:27:03', '2020-08-19 12:27:03'),
(3, 'POS MBim', NULL, '1', '2020-08-19 12:27:03', '2020-08-19 12:27:03'),
(4, 'Cheque', NULL, '1', '2020-08-19 12:27:03', '2020-08-19 12:27:03'),
(5, 'Paga Fácil', NULL, '1', '2020-08-19 12:27:04', '2020-08-19 12:27:04'),
(6, 'POS Moza Banco', NULL, '1', '2020-08-19 12:27:03', '2020-08-19 12:27:03'),
(7, 'POS BCI', NULL, '1', '2020-08-19 12:27:03', '2020-08-19 12:27:03'),
(8, 'Transferência Bancária', NULL, '1', '2020-08-19 12:27:03', '2020-08-19 12:27:03');

-- --------------------------------------------------------

--
-- Estrutura da tabela `unidades`
--

DROP TABLE IF EXISTS `unidades`;
CREATE TABLE IF NOT EXISTS `unidades` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `sigla` varchar(10) NOT NULL,
  `estado` bigint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_unidades_estado` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `unidades`
--

INSERT INTO `unidades` (`id`, `nome`, `sigla`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Metro', 'm', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02'),
(2, 'Peça', 'pc', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02'),
(3, 'Placa', 'pl', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02'),
(4, 'Caixa', 'cx', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02'),
(5, 'Litro', 'L', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02'),
(6, 'Rolo', 'rl', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02'),
(7, 'Saco', 'sc', 1, '2025-08-26 09:06:02', '2025-08-26 09:06:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` int DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `contacto`, `email`, `email_verified_at`, `password`, `estado`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin', 12345, 'admin@admin.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2020-08-19 14:27:06', '2024-09-20 14:43:22'),
(2, 'Gestor', 'gestor', NULL, 'gestor@gestor.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2020-08-18 14:27:06', '2024-12-16 03:08:23'),
(3, 'Adelson Saguate', 'asaguate', 84556632, 'sonnylayson6@gmail.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2024-09-19 13:35:21', '2024-09-20 07:31:49'),
(6, 'Isabel Guivalar', 'isabel', 87645454, 'isabelguivala1@gmail.com', NULL, '$2y$10$nMokZGKyfMu8oAquB6ecoedOslJir45e6hHkDTwq/8Ulh.Tqdiguu', '1', NULL, '2025-08-18 20:34:10', '2025-08-18 20:34:10');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
