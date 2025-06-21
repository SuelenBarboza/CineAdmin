<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($paginaTitulo) ? htmlspecialchars($paginaTitulo) . ' - CineAdmin' : 'CineAdmin' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_admin.css">
    
</head>
<body>
    <header class="cabecalho">
        <a href="painel_admin.php" class="logo-link">
            <div class="logo">
                <i class="fas fa-film"></i>
                <span>CineAdmin</span>
            </div>
        </a>
        
        <nav class="nav-links">
            <a href="painel_admin.php" class="<?= (basename($_SERVER['PHP_SELF'])) === 'painel_admin.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Início
            </a>
            <a href="painel_admin.php?tipo=serie" class="<?= (isset($_GET['tipo'])) && $_GET['tipo'] === 'serie' ? 'active' : '' ?>">
                <i class="fas fa-tv"></i> Séries
            </a>
            <a href="painel_admin.php?tipo=filme" class="<?= (isset($_GET['tipo'])) && $_GET['tipo'] === 'filme' ? 'active' : '' ?>">
                <i class="fas fa-film"></i> Filmes
            </a>
            <a href="destaques.php" class="<?= (basename($_SERVER['PHP_SELF'])) === 'destaques.php' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Destaques
            </a>
            <a href="config_admin.php" class="<?= (basename($_SERVER['PHP_SELF'])) === 'config_admin.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Configurações
            </a>
            
            <a href="suporte_admin.php" class="<?= (basename($_SERVER['PHP_SELF'])) === 'admin_suporte.php' ? 'active' : '' ?>">
                <i class="fas fa-headset"></i> Suporte
            </a>
        </nav>
        
        <div class="busca-usuario">
            <div class="busca">
                <i class="fas fa-search"></i>
                <form method="GET" action="painel_admin.php">
                    <input type="text" name="busca" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                    <input type="hidden" name="tipo" value="<?= $_GET['tipo'] ?? 'todos' ?>">
                </form>
            </div>

            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['admin_foto']) ? htmlspecialchars($_SESSION['admin_foto']) : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="#" onclick="abrirModalMudarFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="config_admin.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="usuarios_admin.php"><i class="fas fa-users-cog"></i> Usuários ADM</a>
                    <a href="logout_admin.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </div>

        <!-- Modal para Mudar Foto -->
        <div id="modal-foto" class="modal">
            <div class="modal-conteudo">
                <div class="modal-cabecalho">
                    <h2><i class="fas fa-camera"></i> Alterar Foto do Perfil</h2>
                    <button class="modal-fechar" onclick="fecharModalFoto()">&times;</button>
                </div>
                
                <form id="form-foto" method="POST" action="upload_foto_admin.php" enctype="multipart/form-data">
                    <div class="form-grupo">
                        <label for="nova-foto">Selecione uma nova imagem</label>
                        <input type="file" id="nova-foto" name="foto_perfil" accept="image/*" required>
                        <img id="preview-foto" src="#" alt="Pré-visualização" style="display: none; max-width: 200px; margin-top: 10px;">
                    </div>
                    
                    <div class="modal-botoes">
                        <button type="button" class="modal-btn secondary" onclick="fecharModalFoto()">
                            Cancelar
                        </button>
                        <button type="submit" class="modal-btn primary">
                            <i class="fas fa-save"></i> Salvar Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>

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

        
    function abrirModalMudarFoto() {
        document.getElementById('modal-foto').style.display = 'flex';
        document.querySelector('.usuario').classList.remove('active');
    }

    function fecharModalFoto() {
        document.getElementById('modal-foto').style.display = 'none';
    }

        
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
</header>