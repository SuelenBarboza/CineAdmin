<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte ao Usuário - CineAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e50914;
            --primary-dark: #b20710;
            --dark: #141414;
            --darker: #0a0a0a;
            --light: #f5f5f5;
            --gray: #808080;
            --gray-dark: #333;
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--darker);
            color: var(--light);
            font-family: 'Sans', 'Helvetica Neue', Arial, sans-serif;
        }

        .conteudo {
            padding: 20px 4%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .suporte-container {
            background-color: var(--dark);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .suporte-titulo {
            color: var(--primary);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .suporte-titulo i {
            font-size: 2rem;
        }

        .suporte-categorias {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .categoria-item {
            background-color: var(--gray-dark);
            padding: 20px;
            border-radius: 6px;
            transition: var(--transition);
            border-left: 4px solid var(--primary);
        }

        .categoria-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .categoria-item h3 {
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .categoria-item ul {
            list-style: none;
            padding-left: 0;
        }

        .categoria-item li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .categoria-item li:before {
            content: "•";
            color: var(--primary);
            position: absolute;
            left: 0;
        }

        .formulario-contato {
            margin-top: 40px;
        }

        .formulario-contato h2 {
            color: var(--primary);
            margin-bottom: 20px;
            border-bottom: 1px solid var(--gray-dark);
            padding-bottom: 10px;
        }

        .form-grupo {
            margin-bottom: 20px;
        }

        .form-grupo label {
            display: block;
            margin-bottom: 8px;
            color: var(--gray);
        }

        .form-grupo input,
        .form-grupo select,
        .form-grupo textarea {
            width: 100%;
            padding: 12px;
            background-color: var(--gray-dark);
            border: 1px solid var(--gray);
            border-radius: 4px;
            color: var(--light);
        }

        .form-grupo textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-enviar {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: var(--transition);
        }

        .btn-enviar:hover {
            background-color: var(--primary-dark);
        }

        @media (max-width: 768px) {
            .suporte-categorias {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include 'header_usuario.php'; ?>
    </header>

    <main class="conteudo">
        <div class="suporte-container">
            <h1 class="suporte-titulo">
                <i class="fas fa-headset"></i> Suporte ao Usuário
            </h1>

            <div class="suporte-categorias">
                <div class="categoria-item">
                    <h3><i class="fas fa-question-circle"></i> Perguntas Frequentes</h3>
                    <ul>
                        <li>Como adicionar itens à minha lista?</li>
                        <li>Como alterar minhas configurações?</li>
                        <li>Problemas com login e senha</li>
                        <li>Como cancelar minha conta?</li>
                    </ul>
                </div>

                <div class="categoria-item">
                    <h3><i class="fas fa-film"></i> Problemas Técnicos</h3>
                    <ul>
                        <li>Vídeos não estão carregando</li>
                        <li>Problemas de reprodução</li>
                        <li>Erros no aplicativo</li>
                        <li>Problemas de conexão</li>
                    </ul>
                </div>

                <div class="categoria-item">
                    <h3><i class="fas fa-credit-card"></i> Assinatura e Pagamentos</h3>
                    <ul>
                        <li>Métodos de pagamento</li>
                        <li>Problemas com cobrança</li>
                        <li>Atualizar forma de pagamento</li>
                        <li>Reembolsos</li>
                    </ul>
                </div>
            </div>

            <div class="formulario-contato">
                <h2><i class="fas fa-envelope"></i> Contate-nos</h2>
                <form action="enviar_suporte.php" method="POST">
                    <div class="form-grupo">
                        <label for="assunto">Assunto</label>
                        <select id="assunto" name="assunto" required>
                            <option value="">Selecione um assunto</option>
                            <option value="tecnico">Problema Técnico</option>
                            <option value="conta">Problema com Conta</option>
                            <option value="pagamento">Problema com Pagamento</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" required></textarea>
                    </div>

                    <button type="submit" class="btn-enviar">
                        <i class="fas fa-paper-plane"></i> Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            
        });
    </script>
</body>
</html>