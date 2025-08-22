-- 1. Estados
INSERT INTO
    estados (nome, descricao)
VALUES
    ('Activo', 'Registo activo no sistema'),
    ('Inactivo', 'Registo inactivo no sistema'),
    ('Pendente', 'Registo pendente');

-- 2. Tipos de Entradas
INSERT INTO
    tipos_entradas (nome, descricao, estado_id)
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
    tipos_saidas (nome, descricao, estado_id)
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
    fornecedores (nome, telefone, email, endereco, estado_id)
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
    clientes (nome, contacto, endereco, estado_id)
VALUES
    (
        'Cliente Exemplo 1',
        '841234569',
        'Av. 1, Maputo',
        1
    );

-- 6. Produtos
INSERT INTO
    produtos (
        codigo_barras,
        nome,
        descricao,
        unidade,
        stock_minimo,
        estado_id
    )
VALUES
    ('PB001', 'Napa', 'Napa', 'unidade', 0, 1),
    (
        'PB002',
        'Veludo HL001',
        'Veludo HL001',
        'unidade',
        0,
        1
    ),
    ('PB003', 'Linho', 'Linho', 'unidade', 0, 1),
    ('PB004', 'Linho RE', 'Linho RE', 'unidade', 0, 1),
    (
        'PB005',
        'Molas grandes',
        'Molas grandes',
        'unidade',
        0,
        1
    ),
    (
        'PB006',
        'Molas pequenas',
        'Molas pequenas',
        'unidade',
        0,
        1
    ),
    (
        'PB007',
        'Mola Quadrada',
        'Mola Quadrada',
        'unidade',
        0,
        1
    ),
    (
        'PB008',
        'prega mola',
        'prega mola',
        'unidade',
        0,
        1
    ),
    (
        'PB009',
        'Cinta elástica grande',
        'Cinta elástica grande',
        'unidade',
        0,
        1
    ),
    (
        'PB010',
        'Cinta elástica pequena',
        'Cinta elástica pequena',
        'unidade',
        0,
        1
    ),
    (
        'PB011',
        'Agrafo 10/13',
        'Agrafo 10/13',
        'unidade',
        0,
        1
    ),
    (
        'PB012',
        'Agrafo 80/12',
        'Agrafo 80/12',
        'unidade',
        0,
        1
    ),
    (
        'PB013',
        'Caixinha de Agrafo montagem N-21',
        'Caixinha de Agrafo montagem N-21',
        'unidade',
        0,
        1
    ),
    ('PB014', 'Encaixo', 'Encaixo', 'unidade', 0, 1),
    (
        'PB015',
        'Cola (contato, Spray) 1L',
        'Cola 1L',
        'unidade',
        0,
        1
    ),
    ('PB016', 'Dacron', 'Dacron', 'unidade', 0, 1),
    (
        'PB017',
        'Rolo Dacron',
        'Rolo Dacron',
        'unidade',
        0,
        1
    ),
    (
        'PB018',
        'Botões diamante',
        'Botões diamante',
        'unidade',
        0,
        1
    ),
    ('PB019', 'Calico', 'Calico', 'unidade', 0, 1),
    (
        'PB020',
        'Pernas Plásticas',
        'Pernas Plásticas',
        'unidade',
        0,
        1
    ),
    (
        'PB021',
        'Aderente pequeno',
        'Aderente pequeno',
        'unidade',
        0,
        1
    ),
    (
        'PB022',
        'Aderente Grande',
        'Aderente Grande',
        'unidade',
        0,
        1
    ),
    ('PB023', 'Saco', 'Saco', 'unidade', 0, 1),
    ('PB024', 'Prego', 'Prego', 'unidade', 0, 1),
    ('PB025', 'Copo', 'Copo', 'unidade', 0, 1),
    ('PB026', 'Pionizes', 'Pionizes', 'unidade', 0, 1),
    (
        'PB027',
        'Placa branca 1 cm',
        'Placa branca 1 cm',
        'unidade',
        0,
        1
    ),
    (
        'PB028',
        'Placa Azul',
        'Placa Azul',
        'unidade',
        0,
        1
    ),
    (
        'PB029',
        'Placa Cinzenta 5cm x 75',
        'Placa Cinzenta 5cm x 75',
        'unidade',
        0,
        1
    ),
    (
        'PB030',
        'Enchimento',
        'Enchimento',
        'unidade',
        0,
        1
    ),
    ('PB031', 'Flocos', 'Flocos', 'unidade', 0, 1),
    (
        'PB032',
        'Placa 10 cm',
        'Placa 10 cm',
        'unidade',
        0,
        1
    );

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
        estado_id
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
        estado_id
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
        estado_id
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
        estado_id
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