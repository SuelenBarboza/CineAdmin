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
        $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['usuario'];
            header('Location: painel_usuario.php');
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
            <h1 class="auth-title">Login do Usuário</h1>

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
                <p>Não tem uma conta? <a href="cadastro_usuario.php">Cadastre-se</a>.</p>
            </div>
        </div>
    </main>
</body>
</html>
