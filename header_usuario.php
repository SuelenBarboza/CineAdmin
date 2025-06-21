<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login_usuario.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_admin.css">
</head>
<body>
<header class="cabecalho">
    <a href="painel_usuario.php" class="logo-link">
        <div class="logo">
            <i class="fas fa-film"></i>
            <span>CineAdmin</span>
        </div>
    </a>

    <nav class="nav-links">
        <a href="painel_usuario.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'painel_usuario.php' && !isset($_GET['tipo'])) ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Início
        </a>
        <a href="painel_usuario.php?tipo=filme" class="<?= (isset($_GET['tipo']) && $_GET['tipo'] === 'filme') ? 'active' : '' ?>">
            <i class="fas fa-film"></i> Filmes
        </a>
        <a href="painel_usuario.php?tipo=serie" class="<?= (isset($_GET['tipo']) && $_GET['tipo'] === 'serie') ? 'active' : '' ?>">
            <i class="fas fa-tv"></i> Séries
        </a>
        <a href="minha_lista.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'minha_lista.php') ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Minha Lista
        </a>
        
    </nav>



    <div class="busca-usuario">
        <div class="busca">
            <i class="fas fa-search"></i>
            <form method="GET" action="painel_usuario.php">
                <input type="text" name="busca" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                <input type="hidden" name="tipo" value="<?= $_GET['tipo'] ?? 'todos' ?>">
                <input type="hidden" name="categoria" value="<?= $_GET['categoria'] ?? 'todas' ?>">
            </form>
        </div>

        <div class="usuario" onclick="toggleDropdown()">
            <img src="<?= isset($_SESSION['usuario_foto']) ? $_SESSION['usuario_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
            <div class="dropdown-usuario">
                <a href="perfil_usuario.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                <a href="#" onclick="abrirModalFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                <a href="config_usuario.php"><i class="fas fa-cog"></i> Configurações</a>
                <a href="suporte_usuario.php"><i class="fas fa-headset"></i> Suporte</a>
                <a href="logout_usuario.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </div>
</header>

<script>
    
    function toggleDropdown() {
        document.querySelector('.usuario').classList.toggle('active');
    }

    
    document.addEventListener('click', function(event) {
        const usuario = document.querySelector('.usuario');
        if (!usuario.contains(event.target)) {
            usuario.classList.remove('active');
        }
    });

   
    document.getElementById('nova-foto').addEventListener('change', function(e) {
        const preview = document.getElementById('preview-foto');
        const file = e.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    });

    
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-foto');
        if (event.target === modal) {
            fecharModalFoto();
        }
    });   
</script>
</body>
</html>