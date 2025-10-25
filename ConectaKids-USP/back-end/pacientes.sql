create database conectaKids;
use conectaKids;

-- Tabela de profissionais
CREATE TABLE profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) NOT NULL,        -- foto obrigatória
    especialidade VARCHAR(100) NOT NULL,      -- especialidade obrigatória
    descricao TEXT NOT NULL,                  -- descrição obrigatória
    perfil_publico TINYINT(1) DEFAULT 0,      -- 1 = público | 0 = privado
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ;


-- Tabela de pacientes
CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) NOT NULL,        -- foto obrigatória
    dificuldade VARCHAR(100) NOT NULL,        -- dificuldade obrigatória
    descricao TEXT NOT NULL,                  -- descrição obrigatória
    perfil_publico TINYINT(1) DEFAULT 0,      -- 1 = público | 0 = privado
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

CREATE TABLE entregas_atividades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  atividade_id INT NOT NULL,
  paciente_id INT NOT NULL,
  nota DECIMAL(5,2) NULL,
  arquivo_entregue VARCHAR(255),
  data_entrega DATE,
  FOREIGN KEY (atividade_id) REFERENCES atividades(id),
  FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
);


select * from pacientes;
select * from profissionais;