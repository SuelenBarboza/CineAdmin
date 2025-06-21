<?php
// index.php
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao CineAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            text-align: center;
            animation: fadeIn 2s ease-in-out;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            animation: slideIn 1.5s ease-in-out;
        }

        .login-options {
            display: none;
            flex-direction: column;
            gap: 20px;
            margin-top: 40px;
        }

        .login-options a {
            padding: 12px 24px;
            font-size: 1.2rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            background: #e50914;
            transition: background 0.3s;
        }

        .login-options a:hover {
            background: #b00710;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .logo-icon {
            font-size: 4rem;
            color: #e50914;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo-icon">
            <i class="fas fa-film"></i>
        </div>
        <h1>Bem-vindo ao CineAdmin</h1>
        <p id="mensagem">Carregando opções...</p>

        <div class="login-options" id="opcoesLogin">
            <a href="login_usuario.php"><i class="fas fa-user"></i> Entrar como Usuário</a>
            <a href="login_admin.php"><i class="fas fa-user-shield"></i> Entrar como Administrador</a>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('mensagem').style.display = 'none';
            document.getElementById('opcoesLogin').style.display = 'flex';
        }, 5000); 
    </script>
</body>
</html>
