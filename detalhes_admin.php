<?php
session_start();


require_once 'conexao.php';


if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: painel_admin.php');
    exit();
}

$id = intval($_GET['id']);
$mysqli = conexao();


$stmt = $mysqli->prepare("SELECT * FROM catalogo WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    header('Location: painel_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['titulo']) ?> - CineView</title>
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

        a {
        text-decoration: none;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Sans', 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: var(--darker);
            color: var(--light);
            overflow-x: hidden;
        }

        
        .cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 4%;
            background-color: var(--dark);
            position: fixed;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        .busca-usuario {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .usuario {
            position: relative;
            cursor: pointer;
        }

        .usuario img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            display: block;
        }

        
        .conteudo {
            padding-top: 70px;
        }

        .voltar-link {
            display: inline-block;
            margin: 20px 4%;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: var(--transition);
        }

        .voltar-link:hover {
            background-color: var(--primary-dark);
        }

        .voltar-link i {
            margin-right: 8px;
        }

        .detalhes-container {
            display: flex;
            padding: 0 4%;
            gap: 30px;
            margin-bottom: 50px;
        }

        .capa-detalhes {
            width: 300px;
            height: 450px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }

        .info-detalhes {
            flex: 1;
        }

        .info-detalhes h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--light);
        }

        .meta-detalhes {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }

        .tipo-detalhes {
            background-color: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .ano-detalhes, .categoria-detalhes {
            color: var(--gray);
            font-size: 1rem;
        }

        .destaque-badge {
            display: inline-block;
            background-color: gold;
            color: #000;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .destaque-badge i {
            margin-right: 5px;
        }

        .descricao {
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

       
        .usuario {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .dropdown-usuario {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown-usuario a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-usuario a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-usuario a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .usuario.active .dropdown-usuario {
            display: block;
        }

        
        @media (max-width: 768px) {
            .detalhes-container {
                flex-direction: column;
            }
            
            .capa-detalhes {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }
            
            .info-detalhes h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .logo span {
                display: none;
            }
            
            .info-detalhes h1 {
                font-size: 1.8rem;
            }
            
            .meta-detalhes {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="cabecalho">
        <a href="painel_admin.php" class="logo-link">
            <div class="logo">
                <i class="fas fa-film"></i>
                <span>CineAdmin</span>
            </div>
        </a>
        
        <div class="busca-usuario">
            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['usuario_foto']) ? $_SESSION['usuario_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php"><i class="fas fa-user"></i> Meu Perfil</a>
                    <a href="logout_admin.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="conteudo">
        <a href="painel_admin.php" class="voltar-link">
            <i class="fas fa-arrow-left"></i> Voltar ao catálogo
        </a>
        
        <div class="detalhes-container">
            <img src="<?= !empty($item['capa']) ? $item['capa'] : 'img/sem-imagem.jpg' ?>" 
                 alt="<?= htmlspecialchars($item['titulo']) ?>" 
                 onerror="this.src='img/sem-imagem.jpg'"
                 class="capa-detalhes">
            
            <div class="info-detalhes">
                <h1><?= htmlspecialchars($item['titulo']) ?></h1>
                
                <div class="meta-detalhes">
                    <span class="tipo-detalhes"><?= strtoupper($item['tipo']) ?></span>
                    <span class="ano-detalhes"><?= $item['ano'] ?></span>
                    <span class="categoria-detalhes"><?= htmlspecialchars($item['categoria']) ?></span>
                </div>
                
                <?php if ($item['destaque']): ?>
                    <div class="destaque-badge">
                        <i class="fas fa-star"></i> DESTAQUE
                    </div>
                <?php endif; ?>
                
                <div class="descricao">
                    <h3>Sinopse</h3>
                    <p><?= nl2br(htmlspecialchars($item['descricao'] ?? 'Sinopse não disponível.')) ?></p>
                </div>
            </div>
        </div>
    </main>
    
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
    </script>
</body>
</html>