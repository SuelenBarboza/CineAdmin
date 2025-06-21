<?php
session_start();
require_once 'conexao.php';  

$error = '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($usuario && $senha) {
        $mysqli = conexao();
        $stmt = $mysqli->prepare("SELECT id, usuario, senha, tipo FROM administradores WHERE usuario = ?");
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $admin = mysqli_fetch_assoc($result);

        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_logado'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_usuario'] = $admin['usuario'];
            header('Location: painel_admin.php');
            exit();
        } else {
            $error = 'Usuário ou senha incorretos!';
        }
    } else {
        $error = 'Usuário e senha são obrigatórios!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header class="cabecalho">
        <a href="index.php" class="logo-link">
        <div class="logo">
            <i class="fas fa-film"></i>
            <span>CineAdmin</span>
        </div>
    </a>
    </header>

    <main class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Acesso Administrativo</h1>
            
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
                    <input type="password" id="senha" name="senha" required>
                    <label for="senha">Senha</label>
                </div>
                
                <button type="submit" class="auth-submit">Entrar</button>
            </form>
            <div class="auth-switch">
                <p>Não tem uma conta? <a href="cadastro_admin.php">Faça o cadastro</a>.</p>
            </div>
        </div>
    </main>
</body>
</html>
