<?php
session_start();


require_once 'conexao.php';


if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}


$is_super_admin = ($_SESSION['admin_nivel'] ?? 0) == 2;


$mysqli = conexao();
$result = $mysqli->query("SELECT id, nome, usuario, email, nivel_acesso, data_criacao FROM administradores");
$administradores = $result->fetch_all(MYSQLI_ASSOC);
$result->free();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    
    if ($acao === 'adicionar_admin' && $is_super_admin) {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $nivel_acesso = intval($_POST['nivel_acesso'] ?? 1);
        
      
        if (empty($nome)) {
            $_SESSION['erro'] = "Por favor, informe o nome.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = "Por favor, informe um e-mail válido.";
        } elseif (strlen($senha) < 6) {
            $_SESSION['erro'] = "A senha deve ter pelo menos 6 caracteres.";
        } else {
            
            $stmt = $mysqli->prepare("SELECT id FROM administradores WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $_SESSION['erro'] = "Este e-mail já está em uso.";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare("INSERT INTO administradores (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $nome, $email, $senha_hash, $nivel_acesso);
                
                if ($stmt->execute()) {
                    $_SESSION['sucesso'] = "Administrador adicionado com sucesso!";
                } else {
                    $_SESSION['erro'] = "Erro ao adicionar administrador: " . $stmt->error;
                }
            }
            $stmt->close();
        }
        
        header('Location: usuarios_admin.php');
        exit();
    }
    
    if ($acao === 'remover_admin' && $is_super_admin) {
        $id = intval($_POST['id'] ?? 0);
        
        
        if ($id == $_SESSION['admin_id']) {
            $_SESSION['erro'] = "Você não pode remover a si mesmo.";
        } else {
            $stmt = $mysqli->prepare("DELETE FROM administradores WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Administrador removido com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao remover administrador: " . $stmt->error;
            }
            $stmt->close();
        }
        
        header('Location: usuarios_admin.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Usuários ADM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_admin.css">
    <style>
        .usuarios-container {
            padding: 20px 4%;
        }
        
        .usuarios-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .usuarios-header h1 {
            font-size: 2rem;
            color: var(--primary);
        }
        
        .btn-adicionar {
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-adicionar:hover {
            background-color: var(--primary-dark);
        }
        
        .tabela-usuarios {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tabela-usuarios th, .tabela-usuarios td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--gray-dark);
        }
        
        .tabela-usuarios th {
            background-color: var(--dark);
            color: var(--primary);
        }
        
        .tabela-usuarios tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .badge-nivel {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .badge-nivel-1 {
            background-color: #4CAF50;
            color: white;
        }
        
        .badge-nivel-2 {
            background-color: #2196F3;
            color: white;
        }
        
        .acoes-usuario button {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            margin-left: 10px;
            transition: color 0.3s;
        }
        
        .acoes-usuario button:hover {
            color: var(--primary);
        }
        
        /* Modal de adição de usuário */
        .modal-adicionar-usuario {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-conteudo {
            background-color: var(--dark);
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 0 30px rgba(229, 9, 20, 0.3);
            border: 1px solid var(--primary);
        }
    </style>
</head>
<body>
    <header class="cabecalho">
        <div class="logo">
            <i class="fas fa-film"></i>
            <span>CineAdmin</span>
        </div>
        
        <nav class="nav-links">
            <a href="painel_admin.php"><i class="fas fa-home"></i> Início</a>
            <a href="painel_admin.php?tipo=serie"><i class="fas fa-tv"></i> Séries</a>
            <a href="painel_admin.php?tipo=filme"><i class="fas fa-film"></i> Filmes</a>
            <a href="destaques.php"><i class="fas fa-star"></i> Destaques</a>
        </nav>
        
        <div class="busca-usuario">
            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['admin_foto']) ? $_SESSION['admin_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="#" onclick="abrirModalMudarFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="config_admin.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="usuarios_admin.php" class="active"><i class="fas fa-users-cog"></i> Usuários ADM</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="conteudo">
        
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
        
        <div class="usuarios-container">
            <div class="usuarios-header">
                <h1><i class="fas fa-users-cog"></i> Usuários Administradores</h1>
                
                <?php if ($is_super_admin): ?>
                    <button class="btn-adicionar" onclick="abrirModalAdicionarUsuario()">
                        <i class="fas fa-plus"></i> Adicionar Admin
                    </button>
                <?php endif; ?>
            </div>
            
            <table class="tabela-usuarios">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Nível de Acesso</th>
                        <th>Data de Criação</th>
                        <?php if ($is_super_admin): ?>
                            <th>Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($administradores as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['nome']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td>
                                <span class="badge-nivel badge-nivel-<?= $admin['nivel_acesso'] ?>">
                                    <?= $admin['nivel_acesso'] == 2 ? 'Super Admin' : 'Admin' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($admin['data_criacao'])) ?></td>
                            <?php if ($is_super_admin): ?>
                                <td class="acoes-usuario">
                                    <?php if ($admin['id'] != $_SESSION['data_criacao']): ?>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="acao" value="remover_admin">
                                            <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                            <button type="submit" onclick="return confirm('Tem certeza que deseja remover este administrador?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    
   
    <?php if ($is_super_admin): ?>
        <div id="modal-adicionar-usuario" class="modal-adicionar-usuario">
            <div class="modal-conteudo">
                <div class="modal-cabecalho">
                    <h2><i class="fas fa-user-plus"></i> Adicionar Administrador</h2>
                    <button class="modal-fechar" onclick="fecharModalAdicionarUsuario()">&times;</button>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="acao" value="adicionar_admin">
                    
                    <div class="form-grupo">
                        <label for="nome">Nome</label>
                        <input type="text" name="nome" id="nome" required>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" required>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="nivel_acesso">Nível de Acesso</label>
                        <select name="nivel_acesso" id="nivel_acesso" required>
                            <option value="1">Administrador</option>
                            <option value="2">Super Administrador</option>
                        </select>
                    </div>
                    
                    <div class="modal-botoes">
                        <button type="button" class="modal-btn secondary" onclick="fecharModalAdicionarUsuario()">
                            Cancelar
                        </button>
                        <button type="submit" class="modal-btn primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
    
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

        
        function abrirModalAdicionarUsuario() {
            document.getElementById('modal-adicionar-usuario').style.display = 'flex';
        }

        function fecharModalAdicionarUsuario() {
            document.getElementById('modal-adicionar-usuario').style.display = 'none';
        }

        
        function abrirModalMudarFoto() {
            document.getElementById('modal-foto').style.display = 'flex';
            document.querySelector('.usuario').classList.remove('active');
        }

        function fecharModalFoto() {
            document.getElementById('modal-foto').style.display = 'none';
        }

        
        window.addEventListener('click', function(event) {
            if (event.target.className === 'modal-adicionar-usuario' || event.target.className === 'modal') {
                fecharModalAdicionarUsuario();
                fecharModalFoto();
            }
        });
    </script>
</body>
</html>