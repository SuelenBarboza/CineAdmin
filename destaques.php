<?php
session_start();


require_once 'conexao.php';


if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}


$mysqli = conexao();
$query = "SELECT * FROM catalogo WHERE destaque = 1 ORDER BY id DESC";
$result = $mysqli->query($query);
$destaques = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Destaques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_admin.css">
    <link rel="stylesheet" href="css/destaques.css">
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
            <a href="painel_admin.php"><i class="fas fa-home"></i> Início</a>
            <a href="painel_admin.php?tipo=serie"><i class="fas fa-tv"></i> Séries</a>
            <a href="painel_admin.php?tipo=filme"><i class="fas fa-film"></i> Filmes</a>
            <a href="destaques.php" class="active"><i class="fas fa-star"></i> Destaques</a>
            <a href="suporte_admin.php"><i class="fas fa-headset"></i> Suporte</a>
        </nav>
        
        <div class="busca-usuario">
            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['admin_foto']) ? $_SESSION['admin_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="#" onclick="abrirModalMudarFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="config_admin.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="usuarios_admin.php"><i class="fas fa-users-cog"></i> Usuários ADM</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="conteudo">
        <div class="destaque-container">
            <div class="destaque-header">
                <h1><i class="fas fa-star"></i> Destaques</h1>
            </div>
            
            <?php if (!empty($destaques)): ?>
                <div class="destaque-grid">
                    <?php foreach ($destaques as $destaque): ?>
                        <div class="destaque-item">
                            <img src="<?= $destaque['capa'] ?>" alt="<?= htmlspecialchars($destaque['titulo']) ?>" onerror="this.src='img/sem-imagem.jpg'">
                            <span class="destaque-badge"><?= strtoupper($destaque['tipo']) ?></span>
                            
                            <button class="remover-destaque-btn" 
                                    onclick="removerDestaque(<?= $destaque['id'] ?>, this.closest('.destaque-item'))"
                                    title="Remover destaque">
                                <i class="fas fa-star"></i> Remover
                            </button>
                            
                            <div class="destaque-info">
                                <h3><?= $destaque['titulo'] ?></h3>
                                <div class="destaque-meta">
                                    <span><?= $destaque['ano'] ?></span>
                                    <span><?= $destaque['categoria'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sem-destaques">
                    <p><i class="fas fa-star" style="color: var(--primary); font-size: 2rem;"></i></p>
                    <p>Nenhum item em destaque no momento.</p>
                    <p>Adicione destaques marcando a opção "Destacar" ao criar ou editar um item.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    
    <div id="modal-confirmar-remocao" class="modal">
        <div class="modal-conteudo" style="max-width: 400px;">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirmar Remoção</h2>
                <button class="modal-fechar" onclick="fecharModalConfirmacao()">&times;</button>
            </div>
            
            <p>Tem certeza que deseja remover este item dos destaques?</p>
            
            <div class="modal-botoes">
                <button type="button" class="modal-btn secondary" onclick="fecharModalConfirmacao()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="modal-btn primary" id="confirmar-remocao-btn">
                    <i class="fas fa-trash"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
    
<script>
        
        let itemParaRemover = null;
        let elementoParaRemover = null;

        
        function toggleDropdown() {
            document.querySelector('.usuario').classList.toggle('active');
        }

        
        document.addEventListener('click', function(event) {
            const usuario = document.querySelector('.usuario');
            if (!usuario.contains(event.target)) {
                usuario.classList.remove('active');
            }
        });

        function removerDestaque(id, elemento) {
            
            itemParaRemover = id;
            elementoParaRemover = elemento;
            
            
            document.getElementById('modal-confirmar-remocao').style.display = 'flex';
        }

        function confirmarRemocao() {
            if (itemParaRemover && elementoParaRemover) {
                fetch('atualizar_destaque.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${itemParaRemover}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        
                        elementoParaRemover.style.transform = 'scale(0.9)';
                        elementoParaRemover.style.opacity = '0';
                        setTimeout(() => {
                            elementoParaRemover.remove();
                            
                            const grid = document.querySelector('.destaque-grid');
                            if (grid && grid.children.length === 0) {
                                grid.innerHTML = `
                                    <div class="sem-destaques">
                                        <p><i class="fas fa-star" style="color: var(--primary); font-size: 2rem;"></i></p>
                                        <p>Nenhum item em destaque no momento.</p>
                                        <p>Adicione destaques marcando a opção "Destacar" ao criar ou editar um item.</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    } else {
                        alert('Erro ao remover destaque: ' + (data.message || 'Erro desconhecido'));
                    }
                    fecharModalConfirmacao();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro na comunicação com o servidor: ' + error.message);
                    fecharModalConfirmacao();
                });
            }
        } 

        function fecharModalConfirmacao() {
            document.getElementById('modal-confirmar-remocao').style.display = 'none';
            itemParaRemover = null;
            elementoParaRemover = null;
        }

        
        document.getElementById('confirmar-remocao-btn').addEventListener('click', confirmarRemocao);
    </script>
</body>
</html>