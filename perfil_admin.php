<?php
session_start();


require_once 'conexao.php';


if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}


require_once 'conexao.php';
$mysqli = conexao();
$admin_id = $_SESSION['admin_id'];
$stmt = $mysqli->prepare("SELECT * FROM administradores WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    
    if ($acao === 'atualizar_perfil') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        
        $stmt = $mysqli->prepare("SELECT id FROM administradores WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['erro'] = "Este e-mail já está em uso por outro administrador.";
        } else {
            $stmt = $mysqli->prepare("UPDATE administradores SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $admin_id);
            
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
                $_SESSION['admin_nome'] = $nome;
                $_SESSION['admin_email'] = $email;
            } else {
                $_SESSION['erro'] = "Erro ao atualizar perfil: " . $stmt->error;
            }
        }
        $stmt->close();
        
        header('Location: perfil_admin.php');
        exit();
    }
    
    if ($acao === 'atualizar_senha') {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        
        
        $stmt = $mysqli->prepare("SELECT senha FROM administradores WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        if (!password_verify($senha_atual, $admin['senha'])) {
            $_SESSION['erro'] = "Senha atual incorreta.";
        } elseif ($nova_senha !== $confirmar_senha) {
            $_SESSION['erro'] = "As novas senhas não coincidem.";
        } elseif (strlen($nova_senha) < 3) {
            $_SESSION['erro'] = "A senha deve ter pelo menos 3 caracteres.";
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE administradores SET senha = ? WHERE id = ?");
            $stmt->bind_param("si", $senha_hash, $admin_id);
            
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Senha atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar senha: " . $stmt->error;
            }
            $stmt->close();
        }
        
        header('Location: perfil_admin.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Meu Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_admin.css">
    <link rel="stylesheet" href="css/perfil_admin.css">
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
            <a href="destaques.php"><i class="fas fa-star"></i> Destaques</a>
            <a href="suporte_admin.php"><i class="fas fa-headset"></i> Suporte</a>
        </nav>
        
        <div class="busca-usuario">
            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['admin_foto']) ? $_SESSION['admin_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php" class="active"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="#" onclick="abrirModalMudarFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="config_admin.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="usuarios_admin.php"><i class="fas fa-users-cog"></i> Usuários ADM</a>
                    <a href="logout_admin.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="conteudo">
        <!-- Mensagens de feedback -->
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="mensagem erro">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['erro'] ?>
                <?php unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="mensagem sucesso">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['sucesso'] ?>
                <?php unset($_SESSION['sucesso']); ?>
            </div>
        <?php endif; ?>
        
        <div class="perfil-container">
            <div class="perfil-header">
                <h1><i class="fas fa-user-cog"></i> Meu Perfil</h1>
            </div>
            
            <div class="perfil-card">
                <div class="perfil-info">
                    <img src="<?= isset($_SESSION['admin_foto']) ? $_SESSION['admin_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Foto do perfil" class="perfil-foto">
                    <div class="perfil-detalhes">
                        <h2><?= htmlspecialchars($admin['nome'] ?? '') ?></h2>
                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($admin['email'] ?? '') ?></p>
                        <p><i class="fas fa-user-tag"></i> <?= htmlspecialchars($admin['nivel_acesso'] ?? 'Administrador') ?></p>
                    </div>
                </div>
                
                <div class="abas">
                    <div class="aba ativa" onclick="mudarAba('dados-pessoais')">Dados Pessoais</div>
                    <div class="aba" onclick="mudarAba('alterar-senha')">Alterar Senha</div>
                </div>
                
                <!-- Aba Dados Pessoais -->
                <div id="dados-pessoais" class="conteudo-aba ativa">
                    <form method="POST" action="">
                        <input type="hidden" name="acao" value="atualizar_perfil">
                        
                        <div class="form-grupo">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($admin['nome'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="modal-botoes">
                            <button type="submit" class="modal-btn primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Aba Alterar Senha -->
                <div id="alterar-senha" class="conteudo-aba">
                    <form method="POST" action="">
                        <input type="hidden" name="acao" value="atualizar_senha">
                        
                        <div class="form-grupo">
                            <label for="senha_atual">Senha Atual</label>
                            <input type="password" name="senha_atual" id="senha_atual" required>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="nova_senha">Nova Senha</label>
                            <input type="password" name="nova_senha" id="nova_senha" required>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="confirmar_senha">Confirmar Nova Senha</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" required>
                        </div>
                        
                        <div class="modal-botoes">
                            <button type="submit" class="modal-btn primary">
                                <i class="fas fa-key"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Modal para Mudar Foto -->
    <div id="modal-foto" class="modal">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-camera"></i> Alterar Foto do Perfil</h2>
                <button class="modal-fechar" onclick="fecharModalFoto()">&times;</button>
            </div>
            
            <form class="modal-formulario" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="mudar_foto">
                
                <div class="form-grupo">
                    <label for="nova-foto">Selecione uma nova imagem</label>
                    <input type="file" name="foto" id="nova-foto" accept="image/*" required>
                </div>
                
                <div class="form-grupo">
                    <img id="preview-foto" src="#" alt="Pré-visualização" style="display: none; max-width: 200px; margin: 10px auto;">
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

       
        function mudarAba(abaId) {
           
            document.querySelectorAll('.aba').forEach(aba => {
                aba.classList.remove('ativa');
            });
            
            document.querySelectorAll('.conteudo-aba').forEach(conteudo => {
                conteudo.classList.remove('ativa');
            });
            
            
            document.querySelector(`.aba[onclick="mudarAba('${abaId}')"]`).classList.add('ativa');
            document.getElementById(abaId).classList.add('ativa');
        }
    </script>
</body>
</html>