CREATE TABLE profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255),           -- novo campo para foto
    especialidade VARCHAR(100),         -- novo campo para especialidade
    descricao TEXT,                     -- novo campo para descrição
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
