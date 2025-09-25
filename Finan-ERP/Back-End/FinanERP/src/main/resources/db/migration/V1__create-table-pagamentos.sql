CREATE TABLE pagamento (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    nome_cliente VARCHAR(100),
    saida VARCHAR(100),
    valor DECIMAL(10,2) NOT NULL,
    data_pagamento DATE NOT NULL,
    descricao VARCHAR(255) NOT NULL
);
