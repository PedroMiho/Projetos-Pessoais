create database conectaKids;
use conectaKids;
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


CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255),           -- novo campo para foto
    dificuldade VARCHAR(100),         -- novo campo para especialidade
    descricao TEXT,                     -- novo campo para descrição
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE vinculos_profissionais_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    paciente_id INT NOT NULL,
    data_vinculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Ativo',  -- Ex: Ativo, Encerrado, Em andamento
    observacoes TEXT,                    -- Campo opcional para observações do profissional

    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    paciente_id INT NOT NULL,
    nome_atividade VARCHAR(255) NOT NULL,
    descricao TEXT,
    arquivo_pdf VARCHAR(255) NOT NULL,
    nota DECIMAL(5,2),
    data_inicio DATE NOT NULL,
    data_encerramento DATE NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);


select * from pacientes;
select * from profissionais;