-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 21-Set-2024 às 13:12
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `www_gestao_armazem`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico`
--

CREATE TABLE `historico` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descricao` text NOT NULL,
  `tabela` varchar(255) NOT NULL,
  `row_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(76, 'Actualizou o produto BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 'produtos', 1, 3, '2024-09-21 12:51:44', '2024-09-21 12:51:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissaos`
--

CREATE TABLE `permissaos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `permissao_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permissao_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `permissao_user`
--

INSERT INTO `permissao_user` (`id`, `permissao_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-09-17 10:43:44', '2024-09-17 10:43:44'),
(2, 2, 2, '2024-09-17 10:43:44', '2024-09-17 10:43:44'),
(3, 3, 3, '2024-09-17 10:43:44', '2024-09-17 10:43:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `quantidade` float NOT NULL DEFAULT 0,
  `stock_minimo` int(5) DEFAULT 2,
  `estado` enum('1','2') NOT NULL DEFAULT '1',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `codigo`, `nome`, `descricao`, `quantidade`, `stock_minimo`, `estado`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '5301000370878', 'BUSKOLAMIN', 'BUSKOLAMIN (BUTILBROMETO DE HIOSCINA) 10MG CX. 20COMP.', 50, 10, '1', NULL, '2024-07-03 14:03:19', '2024-09-21 10:51:44'),
(2, '5301000371912', NULL, 'JODEX SPRAY ORAL (IODOPOVIDONA 8 5%) FR. 30ML', 50, 2, '1', NULL, '2024-07-03 14:05:07', '2024-09-20 15:10:21'),
(3, '5301000370540', NULL, 'METRONIDAZOL 250MG CX. 30COMP.', 70, 2, '1', NULL, '2024-07-03 14:08:53', '2024-09-20 17:43:06'),
(4, '5301000371431', 'DOXYDERMA', 'DOXYDERMA (DOXICICLINA MONOHIDRATADA) 100MG CX. 20COMP.', 190, 50, '1', NULL, '2024-07-03 14:12:08', '2024-09-19 15:24:31'),
(5, '5301000372551', NULL, 'MELIORA (CLORANFENICOL 200MG + METRONIDAZOL 500MG + NISTATINA 660000IU + ACETATO DE HIDROCORTISONA 15MG) CX. 12 OVULOS VAGINAIS', -10, 2, '1', NULL, '2024-07-03 14:15:07', '2024-09-20 15:02:16'),
(6, '5301000371981', NULL, 'MELIOZOL (METRONIDAZOL) 500MG CX. 10 OVULOS VAGINAIS', -10, 2, '1', NULL, '2024-07-04 11:57:22', '2024-09-20 17:39:47'),
(7, '5301000370434', NULL, 'LOSARTAN (LOSARTAN DE POTASSIO) 50MG CX. 30COMP.', 0, 2, '1', NULL, '2024-07-04 11:58:10', '2024-09-19 15:08:51'),
(8, '5301000373817', NULL, 'NEURO B (HIDROCLORETO DE TIAMINA 100MG + CLORIDRATO DE PIRIDOXINA 50MG + CIANOCOBALAMINA 0.5MG) SOLUCAO INJETAVEL 2ML CX. 10 AMPOLAS', 0, 2, '1', NULL, '2024-07-04 12:02:06', '2024-09-19 15:08:51'),
(9, '5301000370311', NULL, 'GLIBENKLAMID (GLIBENCLAMIDA) 5MG CX. 60COMP.', 0, 2, '1', NULL, '2024-07-04 12:04:17', '2024-09-19 15:08:51'),
(10, '5301000371721', NULL, 'GENTAKOL 0.3% (SULFATO DE GENTAMICINA) 5MG/ML GOTAS OFTALMICAS FR. 10ML', 0, 2, '1', NULL, '2024-07-04 12:06:42', '2024-09-19 15:08:51'),
(11, '5301000372148', NULL, 'PILOKARPINE 2% (CLORIDRATO DE PILOCARPINA) 20MG/ML GOTAS OFTALMICAS FR. 10ML', 0, 2, '1', NULL, '2024-07-04 12:09:01', '2024-09-19 15:08:51'),
(12, '5301000371714', NULL, 'ANTIHEMORROIDALE (CLOR. DE EFEDRINA 0.03G + CLOR. DE LIDOCAINA 0.08G + BISMUTO 0.10G + OXIDO DE ZINCO 0.10G + BALSAMO DO PERU 0.10G + OLEO DE RICINO 0.04G) CX. 10 SUPOSITORIOS', 0, 2, '1', NULL, '2024-07-04 12:13:03', '2024-09-19 15:08:51'),
(13, '5301000371684', NULL, 'ADVIT (VITAMINA A 10000 IU + VITAMINA D 10000 IU)/1ML GOTAS ORAIS FR. 20ML', 0, 2, '1', NULL, '2024-07-05 09:10:12', '2024-09-19 15:08:51'),
(14, '5301000370816', NULL, 'KARDIOSPIR (ACIDO ACETILSALICILICO) 100MG CX. 20COMP.', 0, 2, '1', NULL, '2024-07-05 09:11:34', '2024-09-19 15:08:51'),
(15, '5301000371738', NULL, 'BAKTRIM PEDIATRICO (SULFAMETOXAZOL 200MG + TRIMETOPRIM 40MG)/5ML SUSPENSAO ORAL FR. 100ML', 0, 2, '1', NULL, '2024-07-05 09:14:08', '2024-09-19 15:08:51'),
(16, '5301000371790', NULL, 'DEXA-NEO 0.1% + 0.5% (FOSFATO DE SODIO DE DEXAMETASONA + SULFATO DE NEOMICINA) GOTAS OFTALMICAS FR. 5ML', 0, 2, '1', NULL, '2024-07-05 09:18:27', '2024-09-19 15:08:51'),
(17, '53010003772377', NULL, 'KETOFEX XAROPE (FUMARATO DE CETOTIFENO) 1.38MG/5ML FR. 100ML', 0, 2, '1', NULL, '2024-07-09 10:08:03', '2024-09-19 15:08:51'),
(18, '5301000370779', NULL, 'COMPLEXO DE VITAMINA B (CLORIDRATO DE TIAMINA 5MG + CLORIDRATO DE PIRIDOXINA 2MG + RIBOFLAVINA 2MG + NICOTINAMIDA 30MG) CX. 60COMP.', 0, 2, '1', NULL, '2024-07-09 10:11:42', '2024-09-19 15:08:51'),
(19, '5301000370274', NULL, 'ENALAPRIL (MALEATO DE ENALAPRIL) CX. 30COMP.', 0, 2, '1', NULL, '2024-07-09 10:13:05', '2024-09-19 15:08:51'),
(22, '44552', 'Água plus', 'Água plus 600 ml', 0, 5, '1', NULL, '2024-09-18 12:48:38', '2024-09-19 15:08:51'),
(23, '6231', 'adad', 'asfasf', 0, 3, '1', NULL, '2024-09-18 12:52:21', '2024-09-19 15:08:51'),
(24, '44655', 'khkjhkj', 'iohioio', 0, 3, '1', NULL, '2024-09-18 13:01:57', '2024-09-20 15:09:59'),
(25, '44561656', 'Labelo', 'Labelo (Lips Labial)', 0, 5, '1', NULL, '2024-09-18 13:12:39', '2024-09-19 15:08:51'),
(26, '46542', 'NIVEA MEN', 'NIVEA MEN 500 ML', 1000, 10, '1', NULL, '2024-09-18 15:42:06', '2024-09-20 15:11:52'),
(27, '156412', 'Cimento', 'Cimento Leão', 0, 100, '1', NULL, '2024-09-19 12:56:27', '2024-09-19 15:08:51'),
(28, '99089', 'Arroz', 'Arroz leão 50 Kg', 500, 63, '1', 2, '2024-09-19 13:06:02', '2024-09-20 06:51:26'),
(29, '11445', 'Varrão', 'Varrão nr 8', 50, 10, '1', 2, '2024-09-19 13:12:18', '2024-09-20 06:52:08'),
(30, '45645', 'Oleo', 'Oleo Maeva 300 ml', 100, 10, '1', 1, '2024-09-20 06:15:35', '2024-09-20 06:52:29'),
(31, '5454554', 'hjkjddadad', 'asdasdada', 10, 10, '1', 1, '2024-09-21 10:36:14', '2024-09-21 10:36:14'),
(32, '12212', 'Cimento', 'Cimento Dugongo', 10, 20, '1', 1, '2024-09-21 10:40:10', '2024-09-21 10:40:10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `requisicoes`
--

CREATE TABLE `requisicoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` float DEFAULT NULL,
  `estado_requisicao` enum('1','2','3') DEFAULT '1' COMMENT '1 => PENDENTE, 2 => APROVADA, 3 => REPROVADA',
  `estado` enum('1','2') NOT NULL DEFAULT '1',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `requisicoes`
--

INSERT INTO `requisicoes` (`id`, `produto_id`, `quantidade`, `estado_requisicao`, `estado`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 20, '2', '1', 2, '2024-09-20 10:03:24', '2024-09-20 14:33:20'),
(2, 4, 30, '2', '1', 2, '2024-09-20 13:50:09', '2024-09-20 14:59:26'),
(3, 4, 30, '2', '1', 2, '2024-09-20 13:50:18', '2024-09-20 15:00:43'),
(4, 2, 50, '2', '1', 2, '2024-09-20 14:02:42', '2024-09-20 15:10:21'),
(5, 3, 10, '2', '1', 2, '2024-09-20 14:04:05', '2024-09-20 15:02:16'),
(6, 8, 40, '1', '1', 2, '2024-09-20 14:07:01', '2024-09-20 14:07:01'),
(7, 12, 10, '3', '1', 2, '2024-06-11 14:08:17', '2024-09-20 18:33:18'),
(8, 13, 12, '3', '1', 2, '2024-08-07 14:09:23', '2024-09-20 18:32:07'),
(9, 6, 10, '2', '1', 2, '2024-09-20 14:10:07', '2024-09-20 17:39:47'),
(10, 19, 5, '3', '1', 2, '2024-09-20 14:10:18', '2024-09-20 18:31:48'),
(11, 27, 60, '1', '1', 2, '2024-09-20 14:10:27', '2024-09-20 14:10:27'),
(12, 26, 200, '3', '1', 3, '2024-09-20 15:12:12', '2024-09-20 15:13:16'),
(13, 3, 30, '2', '1', 1, '2024-09-20 17:42:52', '2024-09-20 17:43:06'),
(14, 1, 80, '1', '1', 1, '2024-09-21 10:39:38', '2024-09-21 10:39:38'),
(15, 7, 60, '3', '1', 1, '2024-09-21 10:42:16', '2024-09-21 10:42:38');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `contacto` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `estado` enum('1','2') NOT NULL DEFAULT '1',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `contacto`, `email`, `email_verified_at`, `password`, `estado`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin', 12345, 'admin@admin.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2020-08-19 12:27:06', '2024-09-20 12:43:22'),
(2, 'Gestor', 'gestor', NULL, 'gestor@gestor.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2020-08-18 12:27:06', '2024-12-16 01:08:23'),
(3, 'Adelson Saguate', 'asaguate', 84556632, 'sonnylayson6@gmail.com', NULL, '$2y$10$mY.8mcRDvuWraXMbIMX6GOJjKMLH9sHapQrJCX7ae1NfuQIPDvPuW', '1', NULL, '2024-09-19 11:35:21', '2024-09-20 05:31:49');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Índices para tabela `permissaos`
--
ALTER TABLE `permissaos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `permissao_user`
--
ALTER TABLE `permissao_user`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produtos_user_id_foreign` (`user_id`);

--
-- Índices para tabela `requisicoes`
--
ALTER TABLE `requisicoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saidas_user_id_foreign` (`user_id`),
  ADD KEY `activo` (`estado`),
  ADD KEY `estado_pagamento` (`estado_requisicao`),
  ADD KEY `idx_saidas_id` (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `permissaos`
--
ALTER TABLE `permissaos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `permissao_user`
--
ALTER TABLE `permissao_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `requisicoes`
--
ALTER TABLE `requisicoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
