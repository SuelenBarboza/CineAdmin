<?php
session_start();


require_once 'conexao.php';


if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        if ($_POST['acao'] === 'atualizar_perfil') {
            $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
            header('Location: painel_usuario.php');
            exit();
        } elseif ($_POST['acao'] === 'mudar_foto') {
            $diretorio = 'uploads/usuarios/';
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            $nomeArquivo = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
            $caminhoCompleto = $diretorio . $nomeArquivo;
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminhoCompleto)) {
                $_SESSION['usuario_foto'] = $caminhoCompleto;
                $_SESSION['sucesso'] = "Foto atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao enviar a foto. Tente novamente.";
            }
            header('Location: painel_usuario.php');
            exit();
        }
    }
}


$usuario = [
    'nome' => $_SESSION['usuario_nome'] ?? 'Usuário',
    'email' => $_SESSION['usuario_email'] ?? 'usuario@exemplo.com'
];


$filtro_tipo = $_GET['tipo'] ?? 'todos';
$filtro_busca = $_GET['busca'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? 'todas';


$mysqli = conexao();


$result = $mysqli->query("SELECT DISTINCT categoria FROM catalogo");
$categorias = [];
while ($row = $result->fetch_assoc()) {
    $categorias[] = $row['categoria'];
}
sort($categorias);
$result->free();


$query = "SELECT * FROM catalogo WHERE 1=1";
$params = [];
$types = '';

if ($filtro_tipo !== 'todos') {
    $query .= " AND tipo = ?";
    $params[] = $filtro_tipo;
    $types .= 's';
}

if (!empty($filtro_busca)) {
    $query .= " AND titulo LIKE ?";
    $params[] = "%$filtro_busca%";
    $types .= 's';
}

if ($filtro_categoria !== 'todas') {
    $query .= " AND categoria = ?";
    $params[] = $filtro_categoria;
    $types .= 's';
}

$stmt = $mysqli->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$catalogo = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$destaques = array_filter($catalogo, function($item) {
    return $item['destaque'] == 1;
});

$filmes = array_filter($catalogo, function($item) {
    return $item['tipo'] == 'filme';
});

$series = array_filter($catalogo, function($item) {
    return $item['tipo'] == 'serie';
});
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Catálogo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_usuario.css">
        
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
            <a href="painel_usuario.php" class="<?= (!isset($_GET['tipo'])) ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Início
            </a>
            <a href="painel_usuario.php?tipo=filme" class="<?= (isset($_GET['tipo']) && $_GET['tipo'] === 'filme') ? 'active' : '' ?>">
                <i class="fas fa-film"></i> Filmes
            </a>
            <a href="painel_usuario.php?tipo=serie" class="<?= (isset($_GET['tipo']) && $_GET['tipo'] === 'serie') ? 'active' : '' ?>">
                <i class="fas fa-tv"></i> Séries
            </a>
            <a href="minha_lista.php" class="<?= (isset($_GET['lista'])) ? 'active' : '' ?>">
                <i class="fas fa-list"></i> Minha Lista
            </a>
        </nav>
        
        <div class="busca-usuario">
            <div class="busca">
                <i class="fas fa-search"></i>
                <form method="GET" action="">
                    <input type="text" name="busca" placeholder="Buscar..." value="<?= htmlspecialchars($filtro_busca ?? '') ?>">
                    <input type="hidden" name="tipo" value="<?= $filtro_tipo ?? 'todos' ?>">
                    <input type="hidden" name="categoria" value="<?= $filtro_categoria ?? 'todas' ?>">
                </form>
            </div>

            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['usuario_foto']) ? $_SESSION['usuario_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_usuario.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <!-- <a href="#" onclick="abrirModalConfig()"><i class="fas fa-cog"></i> Configurações</a> -->
                    <a href="config_usuario.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="#" onclick="abrirModalFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="suporte_usuario.php"><i class="fas fa-headset"></i> Suporte</a>
                    <a href="logout_usuario.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
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
        
        
        <div class="filtros">
            <h3>Filtrar:</h3>          
            <div class="filtro-grupo">
                <a href="?tipo=todos&categoria=<?= $filtro_categoria ?? 'todas' ?>&busca=<?= urlencode($filtro_busca ?? '') ?>" 
                   class="filtro-btn <?= ($filtro_tipo ?? 'todos') === 'todos' ? 'ativo' : '' ?>">
                   <i class="fas fa-list"></i> Todos
                </a>
                <a href="?tipo=filme&categoria=<?= $filtro_categoria ?? 'todas' ?>&busca=<?= urlencode($filtro_busca ?? '') ?>" 
                   class="filtro-btn <?= ($filtro_tipo ?? 'todos') === 'filme' ? 'ativo' : '' ?>">
                   <i class="fas fa-film"></i> Filmes
                </a>
                <a href="?tipo=serie&categoria=<?= $filtro_categoria ?? 'todas' ?>&busca=<?= urlencode($filtro_busca ?? '') ?>" 
                   class="filtro-btn <?= ($filtro_tipo ?? 'todos') === 'serie' ? 'ativo' : '' ?>">
                   <i class="fas fa-tv"></i> Séries
                </a>
            </div>
            
            <div class="filtro-grupo">
                <select class="filtro-select" onchange="location = this.value;">
                    <option value="?tipo=<?= $filtro_tipo ?? 'todos' ?>&categoria=todas&busca=<?= urlencode($filtro_busca ?? '') ?>" <?= ($filtro_categoria ?? 'todas') === 'todas' ? 'selected' : '' ?>>Todas Categorias</option>
                    <?php foreach (['Ação', 'Aventura', 'Comédia', 'Drama', 'Ficção Científica', 'Terror', 'Romance', 'Animação', 'Documentário', 'Suspense', 'Fantasia', 'Crime', 'Mistério'] as $categoria): ?>
                        <option value="?tipo=<?= $filtro_tipo ?? 'todos' ?>&categoria=<?= urlencode($categoria) ?>&busca=<?= urlencode($filtro_busca ?? '') ?>" <?= ($filtro_categoria ?? 'todas') === $categoria ? 'selected' : '' ?>>
                            <?= $categoria ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        
        <?php if (!empty($destaques)): ?>
            <div class="catalogo-container">
                <h2 class="catalogo-titulo">
                    <i class="fas fa-star"></i> Destaques
                </h2>
                
                <div class="catalogo">
                    <?php foreach ($destaques as $item): ?>
                        <div class="item">
                            <a href="detalhes_usuario.php?id=<?= $item['id'] ?>">
                                <img src="<?= !empty($item['capa']) ? $item['capa'] : 'img/sem-imagem.jpg' ?>" 
                                     alt="<?= htmlspecialchars($item['titulo'] ?? '') ?>" 
                                     onerror="this.src='img/sem-imagem.jpg'">
                                <span class="tipo"><?= strtoupper($item['tipo'] ?? '') ?></span>
                                
                                <div class="info">
                                    <h3><?= htmlspecialchars($item['titulo'] ?? '') ?></h3>
                                    <div class="meta">
                                        <span><?= $item['ano'] ?? '' ?></span>
                                        <span><?= htmlspecialchars($item['categoria'] ?? '') ?></span>
                                    </div>
                                </div>
                            </a>
                            
                            <div class="acoes-usuario">
                                <a href="detalhes_usuario.php?id=<?= $item['id'] ?>" class="acao-btn visualizar" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                           <?php                                
                                $stmt = $mysqli->prepare("SELECT id FROM lista_usuarios WHERE usuario_id = ? AND conteudo_id = ?");
                                $stmt->bind_param("ii", $_SESSION['usuario_id'], $item['id']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $jaNaLista = $result->num_rows > 0;
                                $stmt->close();
                                ?>
                                
                                <button class="acao-btn <?= $jaNaLista ? 'remover' : 'adicionar' ?>" 
                                        onclick="toggleLista(<?= $item['id'] ?>, this)" 
                                        title="<?= $jaNaLista ? 'Remover da lista' : 'Adicionar à lista' ?>">
                                    <i class="fas <?= $jaNaLista ? 'fa-check' : 'fa-plus' ?>"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        
        <?php if ($filtro_tipo === 'todos' || $filtro_tipo === 'filme'): ?>
            <?php if (!empty($filmes)): ?>
                <div class="catalogo-container">
                    <h2 class="catalogo-titulo">
                        <i class="fas fa-film"></i> Filmes
                    </h2>
                    
                    <div class="catalogo">
                        <?php foreach ($filmes as $item): ?>
                            <div class="item">
                                <a href="detalhes_usuario.php?id=<?= $item['id'] ?>">
                                    <img src="<?= !empty($item['capa']) ? $item['capa'] : 'img/sem-imagem.jpg' ?>" 
                                         alt="<?= htmlspecialchars($item['titulo'] ?? '') ?>" 
                                         onerror="this.src='img/sem-imagem.jpg'">
                                    <span class="tipo">FILME</span>
                                    
                                    <div class="info">
                                        <h3><?= htmlspecialchars($item['titulo'] ?? '') ?></h3>
                                        <div class="meta">
                                            <span><?= $item['ano'] ?? '' ?></span>
                                            <span><?= htmlspecialchars($item['categoria'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </a>
                                
                                <div class="acoes-usuario">
                                    <a href="detalhes_usuario.php?id=<?= $item['id'] ?>" class="acao-btn visualizar" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT id FROM lista_usuarios WHERE usuario_id = ? AND conteudo_id = ?");
                                    $stmt->bind_param("ii", $_SESSION['usuario_id'], $item['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $jaNaLista = $result->num_rows > 0;
                                    $stmt->close();
                                    ?>
                                    
                                    <button class="acao-btn <?= $jaNaLista ? 'remover' : 'adicionar' ?>" 
                                            onclick="toggleLista(<?= $item['id'] ?>, this)" 
                                            title="<?= $jaNaLista ? 'Remover da lista' : 'Adicionar à lista' ?>">
                                        <i class="fas <?= $jaNaLista ? 'fa-check' : 'fa-plus' ?>"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        
        <?php if ($filtro_tipo === 'todos' || $filtro_tipo === 'serie'): ?>
            <?php if (!empty($series)): ?>
                <div class="catalogo-container">
                    <h2 class="catalogo-titulo">
                        <i class="fas fa-tv"></i> Séries
                    </h2>
                    
                    <div class="catalogo">
                        <?php foreach ($series as $item): ?>
                            <div class="item">
                                <a href="detalhes_usuario.php?id=<?= $item['id'] ?>">
                                    <img src="<?= !empty($item['capa']) ? $item['capa'] : 'img/sem-imagem.jpg' ?>" 
                                         alt="<?= htmlspecialchars($item['titulo'] ?? '') ?>" 
                                         onerror="this.src='img/sem-imagem.jpg'">
                                    <span class="tipo">SÉRIE</span>
                                    
                                    <div class="info">
                                        <h3><?= htmlspecialchars($item['titulo'] ?? '') ?></h3>
                                        <div class="meta">
                                            <span><?= $item['ano'] ?? '' ?></span>
                                            <span><?= htmlspecialchars($item['categoria'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </a>
                                
                                <div class="acoes-usuario">
                                    <a href="detalhes_usuario.php?id=<?= $item['id'] ?>" class="acao-btn visualizar" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php
                                    $stmt = $mysqli->prepare("SELECT id FROM lista_usuarios WHERE usuario_id = ? AND conteudo_id = ?");
                                    $stmt->bind_param("ii", $_SESSION['usuario_id'], $item['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $jaNaLista = $result->num_rows > 0;
                                    $stmt->close();
                                    ?>
                                    
                                    <button class="acao-btn <?= $jaNaLista ? 'remover' : 'adicionar' ?>" 
                                            onclick="toggleLista(<?= $item['id'] ?>, this)" 
                                            title="<?= $jaNaLista ? 'Remover da lista' : 'Adicionar à lista' ?>">
                                        <i class="fas <?= $jaNaLista ? 'fa-check' : 'fa-plus' ?>"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (empty($destaques) && empty($filmes) && empty($series)): ?>
            <div class="catalogo-container">
                <p>Nenhum item encontrado.</p>
            </div>
        <?php endif; ?>
    </main>
    
    
    <div id="modal-foto" class="modal">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-camera"></i> Alterar Foto do Perfil</h2>
                <button class="modal-fechar" onclick="fecharModalFoto()">&times;</button>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="mudar_foto">
                
                <div class="form-grupo">
                    <label for="foto-perfil">Selecione uma nova foto</label>
                    <input type="file" id="foto-perfil" name="foto_perfil" accept="image/*" onchange="previewFoto(this)">
                    <img id="preview-foto" src="#" alt="Pré-visualização" class="preview-foto">
                </div>
                
                <div class="modal-botoes">
                    <button type="button" class="modal-btn secondary" onclick="fecharModal('modal-foto')">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-btn primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
   
    <div id="modal-config" class="modal">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-user-cog"></i> Configurações</h2>
                <button class="modal-fechar" onclick="fecharModal('modal-config')">&times;</button>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="acao" value="atualizar_perfil">
                
                <div class="form-grupo">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>
                
                <div class="form-grupo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>
                
                <div class="form-grupo">
                    <label for="senha">Nova Senha (deixe em branco para não alterar)</label>
                    <input type="password" id="senha" name="senha">
                </div>
                
                <div class="form-grupo">
                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha">
                </div>
                
                <div class="modal-botoes">
                    <button type="button" class="modal-btn secondary" onclick="fecharModal('modal-config')">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-btn primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        
        function abrirModalFoto() {
            document.getElementById('modal-foto').style.display = 'flex';
            document.querySelector('.usuario').classList.remove('active');
        }
        
        function abrirModalConfig() {
            document.getElementById('modal-config').style.display = 'flex';
            document.querySelector('.usuario').classList.remove('active');
        }
        
        function fecharModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        function toggleDropdown() {
            document.querySelector('.usuario').classList.toggle('active');
        }
        
        
        document.addEventListener('click', function(event) {
            const usuario = document.querySelector('.usuario');
            if (!usuario.contains(event.target)) {
                usuario.classList.remove('active');
            }
            
           
            if (event.target.className === 'modal') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
        
        
        function previewFoto(input) {
            const preview = document.getElementById('preview-foto');
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }
        
        
        let buscaTimeout;
        const buscaInput = document.querySelector('.busca input');
        
        if (buscaInput) {
            buscaInput.addEventListener('input', function() {
                clearTimeout(buscaTimeout);
                buscaTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
        
        
        function toggleLista(conteudoId, botao) {
            const jaNaLista = botao.classList.contains('remover');
            
            fetch('manipular_lista.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `acao=${jaNaLista ? 'remover' : 'adicionar'}&conteudo_id=${conteudoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (jaNaLista) {
                        botao.classList.remove('remover');
                        botao.classList.add('adicionar');
                        botao.innerHTML = '<i class="fas fa-plus"></i>';
                        botao.title = 'Adicionar à lista';
                    } else {
                        botao.classList.remove('adicionar');
                        botao.classList.add('remover');
                        botao.innerHTML = '<i class="fas fa-check"></i>';
                        botao.title = 'Remover da lista';
                    }
                } else {
                    alert('Ocorreu um erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocorreu um erro ao processar sua solicitação');
            });
        }
    </script>
</body>
</html>