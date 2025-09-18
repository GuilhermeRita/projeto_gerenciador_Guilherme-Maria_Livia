-- Criação do banco de dados empresa_tech
CREATE DATABASE IF NOT EXISTS empresa_tech;
USE empresa_tech;

-- Tabela usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Tabela funcionarios
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cargo VARCHAR(255) NOT NULL,
    salario DECIMAL(10,2) NOT NULL
);

-- Tabela projetos
CREATE TABLE IF NOT EXISTS projetos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_projeto VARCHAR(255) NOT NULL,
    descricao TEXT,
    status_projeto VARCHAR(100) NOT NULL
);

-- Tabela alocacao
CREATE TABLE IF NOT EXISTS alocacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT NOT NULL,
    id_projeto INT NOT NULL,
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_projeto) REFERENCES projetos(id) ON DELETE CASCADE
);

-- Inserindo um usuário padrão para teste (senha: admin123)
INSERT INTO usuarios (email, senha) VALUES 
('admin@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserindo alguns funcionários de exemplo
INSERT INTO funcionarios (nome, cargo, salario) VALUES 
('João Silva', 'Desenvolvedor Frontend', 5500.00),
('Maria Santos', 'Desenvolvedora Backend', 6000.00),
('Pedro Oliveira', 'Designer UX/UI', 4800.00),
('Ana Costa', 'Gerente de Projetos', 7500.00);

-- Inserindo alguns projetos de exemplo
INSERT INTO projetos (nome_projeto, descricao, status_projeto) VALUES 
('Sistema E-commerce', 'Desenvolvimento de plataforma de vendas online', 'Em Andamento'),
('App Mobile', 'Aplicativo móvel para gestão de tarefas', 'Planejamento'),
('Portal Corporativo', 'Portal interno da empresa', 'Concluído'),
('API REST', 'API para integração com sistemas externos', 'Em Andamento');

-- Inserindo algumas alocações de exemplo
INSERT INTO alocacao (id_funcionario, id_projeto) VALUES 
(1, 1), -- João no E-commerce
(2, 1), -- Maria no E-commerce
(1, 4), -- João na API
(3, 2), -- Pedro no App Mobile
(4, 1), -- Ana no E-commerce
(4, 2); -- Ana no App Mobile

