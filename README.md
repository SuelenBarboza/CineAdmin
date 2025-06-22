# 🎬 CineAdmin

**CineAdmin** é um sistema web desenvolvido em PHP para gerenciamento de filmes com administradores. Ele permite cadastrar, editar e excluir filmes com informações como título, descrição, capa, ano e gênero, oferecendo uma interface simples e funcional para organização de acervos.

---

## 🚀 Funcionalidades

- Cadastro de filmes com título, descrição, capa e gênero
- Edição e exclusão de filmes
- Listagem com visual moderno
- Upload de imagens de capa
- Sistema de login para administradores
- Tela de painel com controle dos registros

---

## 🛠 Tecnologias utilizadas

- PHP
- MySQL
- HTML5 e CSS3
- JavaScript
- FontAwesome para ícones
- XAMPP para ambiente local

---

## 📂 Estrutura do projeto
/CineAdmin
├── css/
├── img/
├── includes/
├── pages/
├── SQL/
│ └── banco_filmes_admin.sql
├── index.php
├── login.php
├── painel_admin.php
├── cadastrar_filme.php
├── editar_filme.php
├── logout.php
└── Outras páginas


---

## ⚙️ Como rodar o projeto

1. Clone o repositório:

   ```bash
git clone https://github.com/SuelenBarboza/CineAdmin.git
   
Coloque a pasta dentro do diretório htdocs do XAMPP.

Crie o banco de dados MySQL e importe o script banco_filmes_admin.sql localizado na pasta SQL/.

Inicie o Apache e o MySQL pelo XAMPP.

Acesse o sistema via navegador:http://localhost/CineAdmin

💾 Script do banco
O arquivo do banco de dados está na pasta SQL:/SQL/filmes_admin.sql

Você pode importar esse arquivo no phpMyAdmin ou via MySQL Workbench para testar o projeto localmente.

👩‍💻 Autora
Desenvolvido por Suélen Barboza
🔗 https://github.com/SuelenBarboza



