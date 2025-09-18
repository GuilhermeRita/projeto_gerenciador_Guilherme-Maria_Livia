# Gerenciador de Projetos - Sistema Web em PHP

Sistema completo de gerenciamento de projetos desenvolvido em PHP com MySQL, criado como atividade acadêmica para a disciplina de Programação Web da Fatec Guaratinguetá.

---

## 📌 Funcionalidades

- **Login:** Autenticação de usuários via sessões
- **Dashboard:** Visão geral com estatísticas de funcionários, projetos e alocações
- **Gerenciamento de Funcionários:** CRUD completo
- **Gerenciamento de Projetos:** CRUD completo com diferentes status
- **Gerenciamento de Alocações:** Associação de funcionários a projetos
- **Relatórios:** Visualização das alocações recentes e status de projetos

---

## 🏗️ Estrutura do Projeto

projeto_gerenciador/
├── classes/
│ ├── Funcionario.php
│ ├── Projeto.php
│ ├── Usuario.php
│ └── Alocacao.php
├── config/
│ ├── Conexao.php
│ └── session.php
├── assets/
│ └── css/
│ └── style.css
├── sql/
│ └── database.sql
├── login.php
├── logout.php
├── dashboard.php
├── funcionarios.php
├── projetos.php
├── alocacao.php
├── index.php
└── README.md


---

## 🗄️ Banco de Dados

**MySQL (empresa_tech)**

Tabelas:

- `usuarios` → id, email, senha
- `funcionarios` → id, nome, cargo, salario
- `projetos` → id, nome_projeto, descricao, status_projeto
- `alocacao` → id, id_funcionario, id_projeto

Inclui dados de exemplo: 4 funcionários, 4 projetos e várias alocações.

---

## 🚀 Como Usar

### 1️⃣ Configuração do Banco de Dados

1. Abra o phpMyAdmin ou seu MySQL  
2. Execute o script `sql/database.sql`  
3. Verifique as configurações de conexão em `config/Conexao.php`  

### 2️⃣ Configuração do Servidor Web

1. Coloque o projeto na pasta `htdocs` do XAMPP  
2. Certifique-se de que o Apache e o MySQL estão rodando  
3. PHP deve ter a extensão `PDO_MySQL` habilitada  

### 3️⃣ Acesso ao Sistema

- Acesse pelo navegador: `http://localhost/projeto_gerenciador/pages/login.php`  
- Usuário de teste:  
  - **Email:** admin@empresa.com  
  - **Senha:** admin123  

---

## 📝 Observações

- Login sem criptografia de senha para facilitar testes  
- Sessões usadas para controlar autenticação  
- Sistema pronto para rodar localmente no XAMPP  

---

## 💻 Tecnologias

- **Backend:** PHP 7.4+  
- **Banco de Dados:** MySQL 5.7+  
- **Frontend:** HTML5, CSS3, JavaScript  
- **Arquitetura:** MVC simplificada  
- **Segurança:** Prepared statements para prevenir SQL Injection  

---

## ✅ Funcionalidades Implementadas

- CRUD de usuários, funcionários, projetos e alocações  
- Dashboard com estatísticas e relatórios rápidos  
- Login e controle de sessão funcional  
- Interface responsiva e amigável  

---

## 📌 Melhorias Futuras

- Adicionar criptografia de senhas (`password_hash`)  
- Implementar níveis de usuário (admin, gerente, funcionário)  
- Notificações e alertas para alocações  

---
Desenvolvido com ❤️ para aprendizado de PHP e desenvolvimento web
