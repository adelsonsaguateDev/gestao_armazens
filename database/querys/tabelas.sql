-- Estados
CREATE TABLE estados (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Tipos de Entradas
CREATE TABLE tipos_entradas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Tipos de Saídas
CREATE TABLE tipos_saidas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Fornecedores
CREATE TABLE fornecedores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100),
    endereco TEXT,
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Clientes
CREATE TABLE clientes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    contacto VARCHAR(100),
    endereco TEXT,
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Produtos
CREATE TABLE produtos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    codigo_barras VARCHAR(100),
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    unidade VARCHAR(20) NOT NULL,
    stock_minimo INT DEFAULT 0,
    imagem VARCHAR(255),
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estado) REFERENCES estados(id)
);

CREATE TABLE entradas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    tipo_entrada_id BIGINT NOT NULL,
    -- tipo de entrada (compra, transferência, inventário)
    fornecedor_id BIGINT NULL,
    -- fornecedor, se houver
    fornecedor_ref VARCHAR(100),
    -- referência do fornecedor (nome ou código)
    numero_factura VARCHAR(45),
    -- número da factura física
    data_aquisicao DATE NOT NULL,
    -- data em que o produto foi adquirido
    data_factura DATE NOT NULL,
    -- data da factura
    total NUMERIC(12, 2) DEFAULT 0,
    -- total do registo
    total_factura NUMERIC(12, 2) DEFAULT 0,
    -- total da factura
    total_desconto NUMERIC(12, 2) DEFAULT 0,
    -- total de descontos aplicados
    total_iva NUMERIC(12, 2) DEFAULT 0,
    -- total do imposto aplicado
    valor_remanescente NUMERIC(12, 2) DEFAULT 0,
    -- valor restante, se aplicável
    ficheiro_entrada TEXT,
    -- caminho do ficheiro da factura
    user_id BIGINT NULL,
    -- quem registou a entrada
    estado BIGINT NOT NULL DEFAULT 1,
    -- estado do registo (terminado, pendente, etc.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_entrada_id) REFERENCES tipos_entradas(id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Entradas Itens (Lotes)
CREATE TABLE entradas_itens (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    entrada_id BIGINT NOT NULL,
    produto_id BIGINT NOT NULL,
    codigo_barras_lote VARCHAR(100),
    qtd_caixas INT DEFAULT 0,
    qtd_por_caixa INT DEFAULT 1,
    preco_compra_caixa NUMERIC(12, 2) NOT NULL,
    preco_compra_unitario NUMERIC(12, 2) NOT NULL,
    preco_venda_caixa NUMERIC(12, 2) NOT NULL,
    preco_venda_unitario NUMERIC(12, 2) NOT NULL,
    data_validade DATE NULL,
    subtotal NUMERIC(12, 2) GENERATED ALWAYS AS (qtd_caixas * preco_compra_caixa) STORED,
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (entrada_id) REFERENCES entradas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Saídas
DROP TABLE IF EXISTS `saidas`;

CREATE TABLE IF NOT EXISTS `saidas` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id` BIGINT UNSIGNED DEFAULT NULL,
    `tipo_saida_id` BIGINT UNSIGNED NOT NULL,
    `data` DATE NOT NULL,
    `valor_total` DECIMAL(12, 2) DEFAULT NULL,
    `valor_total_iva` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `valor_pago` DECIMAL(12, 2) DEFAULT NULL,
    `valor_remanescente` DECIMAL(12, 2) DEFAULT NULL,
    `desconto` DECIMAL(12, 2) DEFAULT NULL,
    `valor_entregue` DECIMAL(12, 2) DEFAULT NULL,
    `trocos` DECIMAL(12, 2) DEFAULT NULL,
    `tipo_pagamento_id` BIGINT UNSIGNED DEFAULT NULL,
    `numero` INT DEFAULT NULL,
    `numero_cotacao` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `validade_cotacao` DATE DEFAULT NULL,
    `slip` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `numero_factura` VARCHAR(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `estado_pagamento` ENUM('pago', 'nao_pago', 'parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'pago',
    `activo` ENUM('1', '0', '2', '3') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1 => activo, 0 => eliminado, 2 => ..., 3 => devolvida',
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Saídas Itens
DROP TABLE IF EXISTS `saida_itens`;

CREATE TABLE IF NOT EXISTS `saida_itens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `saida_id` BIGINT UNSIGNED NOT NULL,
    `produto_id` BIGINT UNSIGNED NOT NULL,
    `entrada_item_id` BIGINT UNSIGNED DEFAULT NULL,
    `quantidade` DECIMAL(10, 3) NOT NULL,
    `preco_unitario` DECIMAL(12, 2) NOT NULL,
    `preco_compra` DECIMAL(12, 2) NOT NULL,
    `iva` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `valor_iva` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `custo` DECIMAL(12, 2) NOT NULL,
    `desconto_percentual` DECIMAL(5, 2) DEFAULT NULL,
    `desconto_valor` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `tipo_motivo` INT DEFAULT NULL COMMENT '2=>fora_do_prazo, 5=>danificado, 1=>saida_armazem, 4=>oferta, 3=>outro',
    `motivo` TEXT COLLATE utf8mb4_unicode_ci,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `activo` ENUM('1', '0', '2', '3') COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `saida_itens_user_id_foreign` (`user_id`),
    KEY `saida_itens_saida_id_foreign` (`saida_id`),
    KEY `saida_itens_entrada_item_id_foreign` (`entrada_item_id`),
    KEY `idx_produto_id` (`produto_id`),
    KEY `idx_activo` (`activo`),
    KEY `idx_tipo_motivo` (`tipo_motivo`),
    KEY `idx_saida_itens_saida_id` (`saida_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tipo_pagamentos`;
CREATE TABLE IF NOT EXISTS `tipo_pagamentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `designacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
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
COMMIT;


DROP TABLE IF EXISTS `tipo_motivo`;
CREATE TABLE IF NOT EXISTS `tipo_motivo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL,
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
COMMIT;


DROP TABLE IF EXISTS `numeracao`;
CREATE TABLE IF NOT EXISTS `numeracao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL,
  `ano` year NOT NULL,
  `tipo` enum('venda_dinheiro','venda_credito','cotacao','fornecedor','nota_credito_fornecedor','inventario','devolucao','nota_credito','abate','cliente','sessao') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tipo` (`tipo`),
  KEY `ano` (`ano`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

