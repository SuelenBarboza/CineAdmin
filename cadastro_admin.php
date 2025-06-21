<?php
require_once 'conexao.php';

$error = '';
$success = '';
$usuario = '';
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($usuario) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $error = "Todos os campos são obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "E-mail inválido!";
    } elseif ($senha !== $confirmar_senha) {
        $error = "As senhas não coincidem!";
    } elseif (strlen($senha) < 3) {
        $error = "A senha deve ter pelo menos 3 caracteres!";
    } elseif (strlen($usuario) < 4) {
        $error = "O usuário deve ter pelo menos 4 caracteres!";
    } else {
        $mysqli = conexao();
        $stmt = mysqli_prepare($mysqli, "SELECT id FROM administradores WHERE usuario = ?");
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Este usuário já está cadastrado!";
        } else {
            $stmt_email = mysqli_prepare($mysqli, "SELECT id FROM administradores WHERE email = ?");
            mysqli_stmt_bind_param($stmt_email, "s", $email);
            mysqli_stmt_execute($stmt_email);
            mysqli_stmt_store_result($stmt_email);

            if (mysqli_stmt_num_rows($stmt_email) > 0) {
                $error = "Este e-mail já está cadastrado!";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_insert = mysqli_prepare($mysqli, "INSERT INTO administradores (usuario, email, senha) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_insert, "sss", $usuario, $email, $senha_hash);

                if (mysqli_stmt_execute($stmt_insert)) {
                    $redirect = true;
                    $success = "Administrador cadastrado com sucesso!";
                } else {
                    $error = "Erro ao cadastrar administrador. Tente novamente.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Cadastro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/cadastro.css">
    <style>
    
    .success-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    
    .success-message {
        background-color: #4CAF50;
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        width: 250px; 
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        animation: fadeIn 0.3s;
    }
    
    .success-message i {
        font-size: 30px; 
        margin-bottom: 10px;
    }
    
    .success-message h3 {
        font-size: 18px; 
        margin: 10px 0;
    }
    
    .success-message p {
        font-size: 14px; 
        margin: 5px 0;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>
    <?php if ($redirect): ?>
    <div class="success-overlay">
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <h3>Cadastro realizado!</h3>
            <p>Redirecionando para login...</p>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'login_admin.php';
        }, 4000);
    </script>
    <?php endif; ?>

    <header class="cabecalho">
        <div class="logo">
            <i class="fas fa-film"></i>
            <span>CineAdmin</span>
        </div>
    </header>

    <main class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Cadastrar Administrador</h1>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST">
                <div class="form-group">
                    <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario) ?>" required>
                    <label for="usuario">Usuário</label>
                </div>

                <div class="form-group">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Email</label>
                </div>
                
                <div class="form-group password-group">
                    <input type="password" id="senha" name="senha" required>
                    <label for="senha">Senha</label>
                    <span class="eye-icon" onclick="mostrarSenha('senha')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>

                <div class="form-group password-group">
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <span class="eye-icon" onclick="mostrarSenha('confirmar_senha')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <button type="submit" class="auth-submit">Cadastrar</button>
            </form>
            
            <div class="auth-switch">
                <p>Já tem uma conta? <a href="login_admin.php">Faça login</a>.</p>
            </div>
        </div>
    </main>

    <script>
        function mostrarSenha(idCampo) {
            const campo = document.getElementById(idCampo);
            campo.type = campo.type === "password" ? "text" : "password";
        }

        document.querySelectorAll('.form-group input').forEach(input => {
            if (input.value) {
                input.nextElementSibling.style.top = '10px';
                input.nextElementSibling.style.fontSize = '0.8rem';
            }
            
            input.addEventListener('focus', function() {
                this.nextElementSibling.style.top = '10px';
                this.nextElementSibling.style.fontSize = '0.8rem';
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.nextElementSibling.style.top = '20px';
                    this.nextElementSibling.style.fontSize = '1rem';
                }
            });
        });
    </script>
</body>
</html>