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
    tipo_entrada_id BIGINT NOT NULL,         -- tipo de entrada (compra, transferência, inventário)
    fornecedor_id BIGINT NULL,               -- fornecedor, se houver
    fornecedor_ref VARCHAR(100),             -- referência do fornecedor (nome ou código)
    numero_factura VARCHAR(45),              -- número da factura física
    data_aquisicao DATE NOT NULL,            -- data em que o produto foi adquirido
    data_factura DATE NOT NULL,              -- data da factura
    total NUMERIC(12,2) DEFAULT 0,          -- total do registo
    total_factura NUMERIC(12,2) DEFAULT 0,  -- total da factura
    total_desconto NUMERIC(12,2) DEFAULT 0, -- total de descontos aplicados
    total_iva NUMERIC(12,2) DEFAULT 0,      -- total do imposto aplicado
    valor_remanescente NUMERIC(12,2) DEFAULT 0, -- valor restante, se aplicável
    ficheiro_entrada TEXT,                   -- caminho do ficheiro da factura
    user_id BIGINT NULL,                     -- quem registou a entrada
    estado BIGINT NOT NULL DEFAULT 1,     -- estado do registo (terminado, pendente, etc.)
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
    preco_compra_caixa NUMERIC(12,2) NOT NULL,
    preco_compra_unitario NUMERIC(12,2) NOT NULL,
    preco_venda_caixa NUMERIC(12,2) NOT NULL,
    preco_venda_unitario NUMERIC(12,2) NOT NULL,
    data_validade DATE NULL,
    subtotal NUMERIC(12,2) GENERATED ALWAYS AS (qtd_caixas * preco_compra_caixa) STORED,
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (entrada_id) REFERENCES entradas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Saídas
CREATE TABLE saidas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    tipo_saida_id BIGINT NOT NULL,
    data_saida DATE NOT NULL,
    total NUMERIC(12,2) DEFAULT 0,
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_saida_id) REFERENCES tipos_saidas(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);

-- Saídas Itens
CREATE TABLE saidas_itens (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    saida_id BIGINT NOT NULL,
    entrada_item_id BIGINT NOT NULL,
    produto_id BIGINT NOT NULL,
    qtd_caixas INT DEFAULT 0,
    qtd_unidades INT DEFAULT 0,
    preco_venda_unitario NUMERIC(12,2) NOT NULL,
    preco_venda_caixa NUMERIC(12,2),
    subtotal NUMERIC(12,2) GENERATED ALWAYS AS (
        (qtd_caixas * COALESCE(preco_venda_caixa,0)) + (qtd_unidades * preco_venda_unitario)
    ) STORED,
    user_id BIGINT NULL,
    estado BIGINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (saida_id) REFERENCES saidas(id) ON DELETE CASCADE,
    FOREIGN KEY (entrada_item_id) REFERENCES entradas_itens(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (estado) REFERENCES estados(id)
);
