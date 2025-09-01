-- 1. Estados
INSERT INTO
    estados (nome, descricao)
VALUES
    ('Activo', 'Registo activo no sistema'),
    ('Inactivo', 'Registo inactivo no sistema'),
    ('Pendente', 'Registo pendente');

-- 2. Tipos de Entradas
INSERT INTO
    tipos_entradas (nome, descricao, estado)
VALUES
    ('Compra', 'Entrada de produtos via compra', 1),
    (
        'Transferência',
        'Entrada de produtos via transferência',
        1
    ),
    (
        'Inventário',
        'Entrada via ajuste de inventário',
        1
    );

-- 3. Tipos de Saídas
INSERT INTO
    tipos_saidas (nome, descricao, estado)
VALUES
    ('Venda Normal', 'Venda direta a clientes', 1),
    (
        'Venda a Crédito',
        'Venda com pagamento posterior',
        1
    ),
    ('Transferência', 'Saída via transferência', 1);

-- 4. Fornecedores
INSERT INTO
    fornecedores (nome, telefone, email, endereco, estado)
VALUES
    (
        'FENOMENAL COMERCIAL',
        '841234567',
        'fenomenal@exemplo.com',
        'Rua Comercial, Maputo',
        1
    );

-- 5. Clientes
INSERT INTO
    clientes (nome, contacto, endereco, estado)
VALUES
    (
        'Cliente Exemplo 1',
        '841234569',
        'Av. 1, Maputo',
        1
    );

-- Unidades
CREATE TABLE unidades (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    sigla VARCHAR(10) NOT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_unidades_estado FOREIGN KEY (estado) REFERENCES estados(id)
);

INSERT INTO
    unidades (id, nome, sigla, estado)
VALUES
    (1, 'Peça', 'pc', 1),
    (2, 'Metro', 'm', 1),
    (3, 'Placa', 'pl', 1),
    (4, 'Caixa', 'cx', 1),
    (5, 'Litro', 'L', 1),
    (6, 'Rolo', 'rl', 1),
    (7, 'Saco', 'sc', 1);

-- Produtos
INSERT INTO
    produtos (
        codigo_barras,
        nome,
        descricao,
        unidade_id,
        stock_minimo,
        estado
    )
VALUES
    ('PB001', 'Napa', 'Napa', 2, 0, 1),
    -- Metro
    ('PB002', 'Veludo HL001', 'Veludo HL001', 2, 0, 1),
    -- Metro
    ('PB003', 'Linho', 'Linho', 2, 0, 1),
    -- Metro
    ('PB004', 'Linho RE', 'Linho RE', 2, 0, 1),
    -- Metro
    (
        'PB005',
        'Molas grandes',
        'Molas grandes',
        1,
        0,
        1
    ),
    -- Peça
    (
        'PB006',
        'Molas pequenas',
        'Molas pequenas',
        1,
        0,
        1
    ),
    -- Peça
    (
        'PB007',
        'Mola Quadrada',
        'Mola Quadrada',
        1,
        0,
        1
    ),
    -- Peça
    ('PB008', 'Prega mola', 'Prega mola', 1, 0, 1),
    -- Peça
    (
        'PB009',
        'Cinta elástica grande',
        'Cinta elástica grande',
        2,
        0,
        1
    ),
    -- Metro
    (
        'PB010',
        'Cinta elástica pequena',
        'Cinta elástica pequena',
        2,
        0,
        1
    ),
    -- Metro
    ('PB011', 'Agrafo 10/13', 'Agrafo 10/13', 4, 0, 1),
    -- Caixa
    ('PB012', 'Agrafo 80/12', 'Agrafo 80/12', 4, 0, 1),
    -- Caixa
    (
        'PB013',
        'Caixinha de Agrafo montagem N-21',
        'Caixinha de Agrafo montagem N-21',
        4,
        0,
        1
    ),
    -- Caixa
    ('PB014', 'Encaixo', 'Encaixo', 1, 0, 1),
    -- Peça
    (
        'PB015',
        'Cola (contato, Spray) 1L',
        'Cola 1L',
        5,
        0,
        1
    ),
    -- Litro
    ('PB016', 'Dacron', 'Dacron', 2, 0, 1),
    -- Metro
    ('PB017', 'Rolo Dacron', 'Rolo Dacron', 6, 0, 1),
    -- Rolo
    (
        'PB018',
        'Botões diamante',
        'Botões diamante',
        1,
        0,
        1
    ),
    -- Peça
    ('PB019', 'Calico', 'Calico', 2, 0, 1),
    -- Metro
    (
        'PB020',
        'Pernas Plásticas',
        'Pernas Plásticas',
        1,
        0,
        1
    ),
    -- Peça
    (
        'PB021',
        'Aderente pequeno',
        'Aderente pequeno',
        1,
        0,
        1
    ),
    -- Peça
    (
        'PB022',
        'Aderente Grande',
        'Aderente Grande',
        1,
        0,
        1
    ),
    -- Peça
    ('PB023', 'Saco', 'Saco', 7, 0, 1),
    -- Saco
    ('PB024', 'Prego', 'Prego', 1, 0, 1),
    -- Peça
    ('PB025', 'Copo', 'Copo', 1, 0, 1),
    -- Peça
    ('PB026', 'Pionizes', 'Pionizes', 1, 0, 1),
    -- Peça
    (
        'PB027',
        'Placa branca 1 cm',
        'Placa branca 1 cm',
        3,
        0,
        1
    ),
    -- Placa
    ('PB028', 'Placa Azul', 'Placa Azul', 3, 0, 1),
    -- Placa
    (
        'PB029',
        'Placa Cinzenta 5cm x 75',
        'Placa Cinzenta 5cm x 75',
        3,
        0,
        1
    ),
    -- Placa
    ('PB030', 'Enchimento', 'Enchimento', 7, 0, 1),
    -- Saco
    ('PB031', 'Flocos', 'Flocos', 7, 0, 1),
    -- Saco
    ('PB032', 'Placa 10 cm', 'Placa 10 cm', 3, 0, 1);

-- Placa
-- 7. Entradas (exemplo)
INSERT INTO
    entradas (
        tipo_entrada_id,
        fornecedor_id,
        fornecedor_ref,
        numero_factura,
        data_aquisicao,
        data_factura,
        total,
        total_factura,
        total_desconto,
        total_iva,
        valor_remanescente,
        ficheiro_entrada,
        user_id,
        estado
    )
VALUES
    (
        1,
        1,
        'FENOMENAL COMERCIAL',
        'FAC-001/2025',
        '2025-07-30',
        '2025-07-30',
        5000,
        5000,
        0,
        500,
        0,
        'entradas/fac001.pdf',
        1,
        1
    );

-- 8. Entradas Itens (exemplo)
INSERT INTO
    entradas_itens (
        entrada_id,
        produto_id,
        codigo_barras_lote,
        qtd_caixas,
        qtd_por_caixa,
        preco_compra_caixa,
        preco_compra_unitario,
        preco_venda_caixa,
        preco_venda_unitario,
        data_validade,
        user_id,
        estado
    )
VALUES
    (
        1,
        1,
        'PB001-L1',
        10,
        1,
        220,
        220,
        300,
        300,
        '2026-07-30',
        1,
        1
    ),
    (
        1,
        2,
        'PB002-L1',
        5,
        1,
        185,
        185,
        250,
        250,
        '2026-07-30',
        1,
        1
    ),
    (
        1,
        3,
        'PB003-L1',
        8,
        1,
        220,
        220,
        300,
        300,
        '2026-07-30',
        1,
        1
    ),
    (
        1,
        4,
        'PB004-L1',
        12,
        1,
        145,
        145,
        200,
        200,
        '2026-07-30',
        1,
        1
    ),
    (
        1,
        5,
        'PB005-L1',
        2,
        1,
        800,
        800,
        1000,
        1000,
        '2026-07-30',
        1,
        1
    ),
    (
        1,
        6,
        'PB006-L1',
        3,
        1,
        450,
        450,
        600,
        600,
        '2026-07-30',
        1,
        1
    );

-- 1. Saídas (exemplo)
INSERT INTO
    saidas (
        tipo_saida_id,
        data_saida,
        total,
        user_id,
        estado
    )
VALUES
    (1, '2025-08-01', 2500, 1, 1);

-- 2. Saídas Itens (exemplo)
INSERT INTO
    saidas_itens (
        saida_id,
        entrada_item_id,
        produto_id,
        qtd_caixas,
        qtd_unidades,
        preco_venda_unitario,
        preco_venda_caixa,
        user_id,
        estado
    )
VALUES
    (1, 1, 1, 2, 0, 300, 300, 1, 1),
    -- Napa
    (1, 2, 2, 1, 0, 250, 250, 1, 1),
    -- Veludo HL001
    (1, 3, 3, 1, 0, 300, 300, 1, 1),
    -- Linho
    (1, 4, 4, 2, 0, 200, 200, 1, 1),
    -- Linho RE
    (1, 5, 5, 1, 0, 1000, 1000, 1, 1),
    -- Molas grandes
    (1, 6, 6, 1, 0, 600, 600, 1, 1);

-- Molas pequenas
INSERT INTO
    `config` (
        `id`,
        `nome`,
        `endereco`,
        `nuit`,
        `contacto`,
        `contacto2`,
        `email`,
        `slogan`,
        `iva`,
        `fonte`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        NULL,
        'Fenomenal Comercial',
        'Bairro de Mavalane A, Maputo\r\nRua Matchedje nº 4039',
        '400934088',
        '+258 84 531 5759',
        '+258 84 445 6170',
        NULL,
        'Vendas de Material de Mobiliário, Napas e Veludos',
        '1.16',
        '0',
        NULL,
        NULL
    );


    INSERT INTO `tipo_motivo` (`id`, `descricao`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Transferência', '1', '2024-05-02 13:32:57', '2024-05-02 13:32:57'),
(2, 'Fora do prazo', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02'),
(3, 'Outros', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02'),
(4, 'Oferta', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02'),
(5, 'Danificado', '1', '2024-05-02 13:36:02', '2024-05-02 13:36:02');
COMMIT;

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