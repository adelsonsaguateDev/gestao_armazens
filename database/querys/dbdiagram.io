Table estados {
  id BIGINT [pk, increment]
  nome VARCHAR(100)
  descricao TEXT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table tipos_entradas {
  id BIGINT [pk, increment]
  nome VARCHAR(100)
  descricao TEXT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table tipos_saidas {
  id BIGINT [pk, increment]
  nome VARCHAR(100)
  descricao TEXT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table fornecedores {
  id BIGINT [pk, increment]
  nome VARCHAR(150)
  telefone VARCHAR(20)
  email VARCHAR(100)
  endereco TEXT
  user_id BIGINT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table clientes {
  id BIGINT [pk, increment]
  nome VARCHAR(150)
  contacto VARCHAR(100)
  endereco TEXT
  user_id BIGINT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table produtos {
  id BIGINT [pk, increment]
  codigo_barras VARCHAR(100)
  nome VARCHAR(150)
  descricao TEXT
  unidade VARCHAR(20)
  stock_minimo INT
  imagem VARCHAR(255)
  user_id BIGINT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table entradas {
  id BIGINT [pk, increment]
  tipo_entrada_id BIGINT [ref: > tipos_entradas.id]
  fornecedor_id BIGINT [ref: > fornecedores.id]
  fornecedor_ref VARCHAR(100)
  numero_factura VARCHAR(45)
  data_aquisicao DATE
  data_factura DATE
  total NUMERIC(12,2)
  total_factura NUMERIC(12,2)
  total_desconto NUMERIC(12,2)
  total_iva NUMERIC(12,2)
  valor_remanescente NUMERIC(12,2)
  ficheiro_entrada TEXT
  user_id BIGINT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table entradas_itens {
  id BIGINT [pk, increment]
  entrada_id BIGINT [ref: > entradas.id]
  produto_id BIGINT [ref: > produtos.id]
  codigo_barras_lote VARCHAR(100)
  qtd_caixas INT
  qtd_por_caixa INT
  preco_compra_caixa NUMERIC(12,2)
  preco_compra_unitario NUMERIC(12,2)
  preco_venda_caixa NUMERIC(12,2)
  preco_venda_unitario NUMERIC(12,2)
  data_validade DATE
  subtotal NUMERIC(12,2)
  user_id BIGINT
  estado BIGINT [ref: > estados.id]
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table saidas {
  id BIGINT [pk, increment]
  cliente_id BIGINT [ref: > clientes.id]
  tipo_saida_id BIGINT [ref: > tipos_saidas.id]
  data DATE
  valor_total DECIMAL(12,2)
  valor_total_iva DECIMAL(12,2)
  valor_pago DECIMAL(12,2)
  valor_remanescente DECIMAL(12,2)
  desconto DECIMAL(12,2)
  valor_entregue DECIMAL(12,2)
  trocos DECIMAL(12,2)
  tipo_pagamento_id BIGINT [ref: > tipo_pagamentos.id]
  numero INT
  numero_cotacao VARCHAR(50)
  validade_cotacao DATE
  slip VARCHAR(255)
  numero_factura VARCHAR(45)
  estado_pagamento ENUM
  activo ENUM
  user_id BIGINT
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table saida_itens {
  id BIGINT [pk, increment]
  saida_id BIGINT [ref: > saidas.id]
  produto_id BIGINT [ref: > produtos.id]
  entrada_item_id BIGINT [ref: > entradas_itens.id]
  quantidade DECIMAL(10,3)
  preco_unitario DECIMAL(12,2)
  preco_compra DECIMAL(12,2)
  iva DECIMAL(12,2)
  valor_iva DECIMAL(12,2)
  custo DECIMAL(12,2)
  desconto_percentual DECIMAL(5,2)
  desconto_valor DECIMAL(12,2)
  tipo_motivo INT [ref: > tipo_motivo.id]
  motivo TEXT
  user_id BIGINT
  activo ENUM
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table tipo_pagamentos {
  id BIGINT [pk, increment]
  designacao VARCHAR(255)
  user_id BIGINT
  is_active ENUM
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table tipo_motivo {
  id INT [pk, increment]
  descricao VARCHAR(40)
  estado ENUM
  created_at DATETIME
  updated_at DATETIME
}

Table numeracao {
  id INT [pk, increment]
  numero INT
  ano YEAR
  tipo ENUM
  created_at DATETIME
  updated_at DATETIME
}

Table config {
  id BIGINT [pk, increment]
  nome VARCHAR(255)
  endereco VARCHAR(255)
  nuit VARCHAR(255)
  contacto VARCHAR(255)
  contacto2 VARCHAR(255)
  email VARCHAR(255)
  slogan VARCHAR(255)
  iva DECIMAL(8,2)
  fonte VARCHAR(255)
  created_at TIMESTAMP
  updated_at TIMESTAMP
}

Table pagamentos {
  id BIGINT [pk, increment]
  valor_pago DOUBLE(12,2)
  valor_pago2 DOUBLE(12,2)
  cambio FLOAT
  numero_recibo VARCHAR(45)
  data_pagamento DATETIME
  numero VARCHAR(45)
  tipo_pagamento_id BIGINT [ref: > tipo_pagamentos.id]
  cliente_id BIGINT [ref: > clientes.id]
  saida_id BIGINT [ref: > saidas.id]
  user_id BIGINT
  created_at DATETIME
  updated_at DATETIME
}
