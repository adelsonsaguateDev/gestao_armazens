-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 26-Ago-2025 às 06:46
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
  `estado_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `total` decimal(12,2) DEFAULT '0.00',
  `total_factura` decimal(12,2) DEFAULT '0.00',
  `total_desconto` decimal(12,2) DEFAULT '0.00',
  `total_iva` decimal(12,2) DEFAULT '0.00',
  `valor_remanescente` decimal(12,2) DEFAULT '0.00',
  `ficheiro_entrada` text,
  `user_id` bigint DEFAULT NULL,
  `estado_id` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tipo_entrada_id` (`tipo_entrada_id`),
  KEY `fornecedor_id` (`fornecedor_id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `preco_compra_caixa` decimal(12,2) NOT NULL,
  `preco_compra_unitario` decimal(12,2) NOT NULL,
  `preco_venda_caixa` decimal(12,2) NOT NULL,
  `preco_venda_unitario` decimal(12,2) NOT NULL,
  `data_validade` date DEFAULT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`qtd_caixas` * `preco_compra_caixa`)) STORED,
  `user_id` bigint DEFAULT NULL,
  `estado_id` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `entrada_id` (`entrada_id`),
  KEY `produto_id` (`produto_id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `estado_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(94, 'Registou a requisição 25.', 'requisicoes', 25, 1, '2025-08-22 10:26:04', '2025-08-22 10:26:04');

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
  `unidade` varchar(20) NOT NULL,
  `stock_minimo` int DEFAULT '0',
  `imagem` varchar(255) DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `estado_id` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id` bigint NOT NULL AUTO_INCREMENT,
  `tipo_saida_id` bigint NOT NULL,
  `data_saida` date NOT NULL,
  `total` decimal(12,2) DEFAULT '0.00',
  `user_id` bigint DEFAULT NULL,
  `estado_id` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tipo_saida_id` (`tipo_saida_id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saidas_itens`
--

DROP TABLE IF EXISTS `saidas_itens`;
CREATE TABLE IF NOT EXISTS `saidas_itens` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `saida_id` bigint NOT NULL,
  `entrada_item_id` bigint NOT NULL,
  `produto_id` bigint NOT NULL,
  `qtd_caixas` int DEFAULT '0',
  `qtd_unidades` int DEFAULT '0',
  `preco_venda_unitario` decimal(12,2) NOT NULL,
  `preco_venda_caixa` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS (((`qtd_caixas` * coalesce(`preco_venda_caixa`,0)) + (`qtd_unidades` * `preco_venda_unitario`))) STORED,
  `user_id` bigint DEFAULT NULL,
  `estado_id` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `saida_id` (`saida_id`),
  KEY `entrada_item_id` (`entrada_item_id`),
  KEY `produto_id` (`produto_id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos_entradas`
--

DROP TABLE IF EXISTS `tipos_entradas`;
CREATE TABLE IF NOT EXISTS `tipos_entradas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text,
  `estado_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos_saidas`
--

DROP TABLE IF EXISTS `tipos_saidas`;
CREATE TABLE IF NOT EXISTS `tipos_saidas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text,
  `estado_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `contacto`, `email`, `email_verified_at`, `password`, `estado`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin', 12345, 'admin@admin.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2020-08-19 12:27:06', '2024-09-20 12:43:22'),
(2, 'Gestor', 'gestor', NULL, 'gestor@gestor.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2020-08-18 12:27:06', '2024-12-16 01:08:23'),
(3, 'Adelson Saguate', 'asaguate', 84556632, 'sonnylayson6@gmail.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2024-09-19 11:35:21', '2024-09-20 05:31:49'),
(6, 'Isabel Guivalar', 'isabel', 87645454, 'isabelguivala1@gmail.com', NULL, '$2y$10$nMokZGKyfMu8oAquB6ecoedOslJir45e6hHkDTwq/8Ulh.Tqdiguu', '1', NULL, '2025-08-18 18:34:10', '2025-08-18 18:34:10');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
