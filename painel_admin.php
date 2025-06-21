<?php
session_start();


require_once 'conexao.php';


if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

if (mysqli_connect_errno()) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}


function uploadArquivo($arquivo, $pasta) {
    $diretorioBase = __DIR__ . '/uploads/';
    
    if (!file_exists($diretorioBase)) {
        if (!mkdir($diretorioBase, 0755, true)) {
            error_log("Falha ao criar diretório uploads");
            return false;
        }
    }

    $diretorio = $diretorioBase . $pasta . '/';
    if (!file_exists($diretorio)) {
        if (!mkdir($diretorio, 0755, true)) {
            error_log("Falha ao criar diretório para $pasta");
            return false;
        }
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        error_log("Erro no upload: " . $arquivo['error']);
        return false;
    }

    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($mime, $tiposPermitidos)) {
        error_log("Tipo de arquivo não permitido: $mime");
        return false;
    }

    $nomeArquivo = uniqid() . '_' . basename($arquivo['name']);
    $caminhoCompleto = $diretorio . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return "uploads/$pasta/$nomeArquivo";
    } else {
        error_log("Falha ao mover arquivo para $caminhoCompleto");
        return false;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    
    switch ($acao) {
        case 'adicionar':
            $titulo = trim($_POST['titulo'] ?? '');
            $tipo = $_POST['tipo'] ?? 'filme';
            $ano = $_POST['ano'] ?? date('Y');
            $categoria = trim($_POST['categoria'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $destaque = isset($_POST['destaque']) ? 1 : 0;

            $capa = '';
            if (!empty($_FILES['capa']['name'])) {
                $capa = uploadArquivo($_FILES['capa'], 'capas');
                if (!$capa) {
                    $_SESSION['erro'] = "Falha ao fazer upload da capa. Verifique se é uma imagem válida (JPEG, PNG ou GIF) e menor que 2MB.";
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            } else {
                $_SESSION['erro'] = "Por favor, selecione uma capa";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }


            require_once 'conexao.php';
            $mysqli = conexao();
            $stmt = $mysqli->prepare("INSERT INTO catalogo (titulo, tipo, capa, ano, categoria, descricao, destaque) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $titulo, $tipo, $capa, $ano, $categoria, $descricao, $destaque);
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Item adicionado com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao adicionar item: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'editar':
            $id = intval($_POST['id'] ?? 0);
            $titulo = trim($_POST['titulo'] ?? '');
            $tipo = $_POST['tipo'] ?? 'filme';
            $ano = $_POST['ano'] ?? date('Y');
            $categoria = trim($_POST['categoria'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $destaque = isset($_POST['destaque']) ? 1 : 0;
            $capa_atual = $_POST['capa_atual'] ?? '';

            if (!empty($_FILES['capa']['name'])) {
                $capa = uploadArquivo($_FILES['capa'], 'capas');
                if (!$capa) {
                    $_SESSION['erro'] = "Falha ao fazer upload da nova capa. Verifique se é uma imagem válida (JPEG, PNG ou GIF) e menor que 2MB.";
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
                $mysqli = conexao();
                $stmt = $mysqli->prepare("UPDATE catalogo SET titulo = ?, tipo = ?, capa = ?, ano = ?, categoria = ?, descricao = ?, destaque = ? WHERE id = ?");
                $stmt->bind_param("sssissii", $titulo, $tipo, $capa, $ano, $categoria, $descricao, $destaque, $id);
            } else {
                require_once 'conexao.php';
                $mysqli = conexao();
                $stmt = $mysqli->prepare("UPDATE catalogo SET titulo = ?, tipo = ?, ano = ?, categoria = ?, descricao = ?, destaque = ? WHERE id = ?");
                $stmt->bind_param("ssissii", $titulo, $tipo, $ano, $categoria, $descricao, $destaque, $id);
            }

            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Item atualizado com sucesso!";
                if (!empty($_FILES['capa']['name']) && !empty($capa_atual) && file_exists(__DIR__ . '/' . $capa_atual)) {
                    unlink(__DIR__ . '/' . $capa_atual);
                }
            } else {
                $_SESSION['erro'] = "Erro ao atualizar item: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'excluir':
            $mysqli = conexao();
            $id = intval($_POST['id'] ?? 0);
            $stmt = $mysqli->prepare("SELECT capa FROM catalogo WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $stmt->close();

            $stmt = $mysqli->prepare("DELETE FROM catalogo WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Item excluído com sucesso!";
                if (!empty($item['capa']) && file_exists(__DIR__ . '/' . $item['capa'])) {
                    unlink(__DIR__ . '/' . $item['capa']);
                }
            } else {
                $_SESSION['erro'] = "Erro ao excluir item: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'mudar_foto':
            if (!empty($_FILES['foto']['name'])) {
                $foto = uploadArquivo($_FILES['foto'], 'usuarios');
                if ($foto) {
                    // Remove a foto antiga se existir
                    if (!empty($_SESSION['admin_foto'])) {
                        $fotoAntiga = __DIR__ . str_replace('uploads/', '/uploads/', $_SESSION['admin_foto']);
                        if (file_exists($fotoAntiga) && is_file($fotoAntiga)) {
                            unlink($fotoAntiga);
                        }
                    }
                    // Atualiza a sessão com o novo caminho (relativo ao root)
                    $_SESSION['admin_foto'] = '/' . ltrim($foto, '/');
                    $_SESSION['sucesso'] = "Foto atualizada com sucesso!";
                } else {
                    $_SESSION['erro'] = "Falha ao atualizar a foto. Verifique se é uma imagem válida (JPEG, PNG ou GIF) e menor que 2MB.";
                }
            } else {
                $_SESSION['erro'] = "Nenhuma imagem foi selecionada.";
            }
            break;
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}


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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Plataforma de Gestão</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/painel_admin.css">
    <style>
        
.modal-conteudo {
    max-height: 90vh; 
    overflow-y: auto; 
    width: 90%; 
    max-width: 800px; 
}


.modal-corpo {
    padding: 20px;
}


.modal-conteudo::-webkit-scrollbar {
    width: 8px;
}

.modal-conteudo::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-conteudo::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.modal-conteudo::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Estilo para o container do usuário */
.usuario {
    position: relative;
    cursor: pointer;
    z-index: 1000; /* Garante que fique acima de outros elementos */
}

/* Estilo para o dropdown */
.dropdown-usuario {
    position: absolute;
    right: 0;
    top: 100%;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 200px;
    display: none;
    z-index: 1001; /* Acima da foto */
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Quando o dropdown está ativo */
.usuario.active .dropdown-usuario {
    display: block;
    opacity: 1;
}

/* Estilo para os itens do dropdown */
.dropdown-usuario a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
}

.dropdown-usuario a:hover {
    background: #f5f5f5;
}

/* Garante que a imagem do usuário tenha um z-index menor que o dropdown */
.usuario img {
    position: relative;
    z-index: 999;
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
        
        <nav class="nav-links">
            <a href="painel_admin.php" class="<?= (!isset($_GET['tipo'])) ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Início
            </a>
            <a href="painel_admin.php?tipo=serie" class="<?= (isset($_GET['tipo']) && $_GET['tipo'] === 'serie') ? 'active' : '' ?>">
                <i class="fas fa-tv"></i> Séries
            </a>
            <a href="painel_admin.php?tipo=filme" class="<?= (isset($_GET['tipo']) && $_GET['tipo'] === 'filme') ? 'active' : '' ?>">
                <i class="fas fa-film"></i> Filmes
            </a>
            <a href="destaques.php" class="<?= (basename($_SERVER['PHP_SELF'])) === 'destaques.php' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Destaques
            </a>

             <a href="suporte_admin.php" class="<?= (basename($_SERVER['PHP_SELF'])) === 'admin_suporte.php' ? 'active' : '' ?>">
                <i class="fas fa-headset"></i> Suporte
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

            <div class="usuario" onclick="toggleDropdown(event)">
                <img src="<?= isset($_SESSION['admin_foto']) ? $_SESSION['admin_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="#" onclick="abrirModalMudarFoto(event)"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="config_admin.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="usuarios_admin.php"><i class="fas fa-users-cog"></i> Usuários ADM</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
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
        
        <!-- Painel de Controle -->
        <div class="painel-controle">
            <h2>Painel de Administração</h2>
            <button class="btn-adicionar" onclick="abrirModalAdicionar()">
                <i class="fas fa-plus"></i> Adicionar Item
            </button>
        </div>
        
        <!-- Filtros -->
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
        
        <!-- Catálogo -->
        <div class="catalogo-container">
            <h2 class="catalogo-titulo">
                <i class="fas fa-list-ul"></i>
                <?= ($filtro_tipo ?? 'todos') === 'todos' ? 'Todos os Itens' : (($filtro_tipo ?? 'todos') === 'filme' ? 'Filmes' : 'Séries') ?>
                <?= ($filtro_categoria ?? 'todas') !== 'todas' ? 'em ' . ($filtro_categoria ?? 'todas') : '' ?>
                <?= !empty($filtro_busca) ? 'com "' . htmlspecialchars($filtro_busca) . '"' : '' ?>
            </h2>
            
            <div class="catalogo">
                <?php if (!empty($catalogo)): ?>
                    <?php foreach ($catalogo as $item): ?>
                        <div class="item">
                            <img src="<?= (!empty($item['capa'])) ? $item['capa'] : 'img/sem-imagem.jpg' ?>" 
                                 alt="<?= htmlspecialchars($item['titulo'] ?? '') ?>" 
                                 onerror="this.src='img/sem-imagem.jpg'"
                                 class="capa-item">
                            <span class="tipo"><?= strtoupper($item['tipo'] ?? '') ?></span>
                            
                            <div class="info">
                                <h3><?= $item['titulo'] ?? '' ?></h3>
                                <div class="meta">
                                    <span><?= $item['ano'] ?? '' ?></span>
                                    <span><?= $item['categoria'] ?? '' ?></span>
                                </div>
                            </div>
                            
                            <div class="acoes-admin">
                                <a href="detalhes_admin.php?id=<?= $item['id'] ?>" class="acao-btn" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <button class="acao-btn" onclick="abrirModalEditar(<?= $item['id'] ?? 0 ?>, '<?= addslashes($item['titulo'] ?? '') ?>', '<?= $item['tipo'] ?? '' ?>', '<?= addslashes($item['capa'] ?? '') ?>', <?= $item['ano'] ?? 0 ?>, '<?= addslashes($item['categoria'] ?? '') ?>', '<?= addslashes($item['descricao'] ?? '') ?>', <?= $item['destaque'] ?? 0 ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="acao-btn" onclick="confirmarExclusao(<?= $item['id'] ?? 0 ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="acao-btn destaque <?= ($item['destaque'] ?? 0) ? 'ativo' : '' ?>" onclick="toggleDestaque(<?= $item['id'] ?? 0 ?>, this)">
                                    <i class="fas fa-star"></i>
                                </button>

                                
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>  
                    <p>Nenhum item encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Modal de Adição -->
    <div id="modal-adicionar" class="modal">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-plus-circle"></i> Adicionar Item</h2>
                <button class="modal-fechar" onclick="fecharModal()">&times;</button>
            </div>
            
            <form class="modal-formulario" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="adicionar">
                
                <div class="form-grupo">
                    <label for="adicionar-titulo">Título</label>
                    <input type="text" name="titulo" id="adicionar-titulo" placeholder="Título do filme/série" required>
                </div>
                
                <div class="form-grupo">
                    <label for="adicionar-tipo">Tipo</label>
                    <select name="tipo" id="adicionar-tipo" required>
                        <option value="filme">Filme</option>
                        <option value="serie">Série</option>
                    </select>
                </div>
                
                <div class="form-grupo">
                    <label for="adicionar-capa">Capa do Filme/Série</label>
                    <input type="file" name="capa" id="adicionar-capa" accept="image/*" required>
                    <img id="preview-capa-adicionar" src="#" alt="Pré-visualização" style="display: none; max-width: 200px; margin-top: 10px;">
                </div>
                
                <div class="form-grupo">
                    <label for="adicionar-ano">Ano de Lançamento</label>
                    <input type="number" name="ano" id="adicionar-ano" placeholder="Ano" min="1900" max="<?= date('Y') ?>" required>
                </div>
                
                <div class="form-grupo">
                    <label for="adicionar-categoria">Categoria</label>
                    <select name="categoria" id="adicionar-categoria" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="Ação">Ação</option>
                        <option value="Aventura">Aventura</option>
                        <option value="Comédia">Comédia</option>
                        <option value="Drama">Drama</option>
                        <option value="Ficção Científica">Ficção Científica</option>
                        <option value="Terror">Terror</option>
                        <option value="Romance">Romance</option>
                        <option value="Animação">Animação</option>
                        <option value="Documentário">Documentário</option>
                        <option value="Suspense">Suspense</option>
                        <option value="Fantasia">Fantasia</option>
                        <option value="Crime">Crime</option>
                        <option value="Mistério">Mistério</option>
                    </select>
                </div>
                
                <div class="form-grupo">
                    <label for="adicionar-descricao">Descrição</label>
                    <textarea name="descricao" id="adicionar-descricao" placeholder="Sinopse ou descrição"></textarea>
                </div>
                
                <div class="form-grupo">
                    <label>
                        <input type="checkbox" name="destaque" id="adicionar-destaque"> Destacar na página inicial
                    </label>
                </div>
                
                <div class="modal-botoes">
                    <button type="button" class="modal-btn secondary" onclick="fecharModal()">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-btn primary">
                        <i class="fas fa-save"></i> Adicionar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Edição -->
    <div id="modal-editar" class="modal">
    <div class="modal-conteudo">
        <div class="modal-cabecalho">
            <h2><i class="fas fa-edit"></i> Editar Item</h2>
            <button class="modal-fechar" onclick="fecharModal()">&times;</button>
        </div>

        <div class="modal-corpo">           
            <form class="modal-formulario" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" id="editar-id">
                <input type="hidden" id="capa-atual" name="capa_atual">
                
                <div class="form-grupo">
                    <label for="editar-titulo">Título</label>
                    <input type="text" name="titulo" id="editar-titulo" placeholder="Título do filme/série" required>
                </div>
                
                <div class="form-grupo">
                    <label for="editar-tipo">Tipo</label>
                    <select name="tipo" id="editar-tipo" required>
                        <option value="filme">Filme</option>
                        <option value="serie">Série</option>
                    </select>
                </div>
                
                <div class="form-grupo">
                    <label for="editar-capa">Capa do Filme/Série (Deixe em branco para manter a atual)</label>
                    <input type="file" name="capa" id="editar-capa" accept="image/*">
                    <img id="preview-capa-editar" src="#" alt="Pré-visualização" style="display: none; max-width: 200px; margin-top: 10px;">
                </div>
                
                <div class="form-grupo">
                    <label for="editar-ano">Ano de Lançamento</label>
                    <input type="number" name="ano" id="editar-ano" placeholder="Ano" min="1900" max="<?= date('Y') ?>" required>
                </div>
                
                <div class="form-grupo">
                    <label for="editar-categoria">Categoria</label>
                    <select name="categoria" id="editar-categoria" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="Ação">Ação</option>
                        <option value="Aventura">Aventura</option>
                        <option value="Comédia">Comédia</option>
                        <option value="Drama">Drama</option>
                        <option value="Ficção Científica">Ficção Científica</option>
                        <option value="Terror">Terror</option>
                        <option value="Romance">Romance</option>
                        <option value="Animação">Animação</option>
                        <option value="Documentário">Documentário</option>
                        <option value="Suspense">Suspense</option>
                        <option value="Fantasia">Fantasia</option>
                        <option value="Crime">Crime</option>
                        <option value="Mistério">Mistério</option>
                    </select>
                </div>
                
                <div class="form-grupo">
                    <label for="editar-descricao">Descrição</label>
                    <textarea name="descricao" id="editar-descricao" placeholder="Sinopse ou descrição"></textarea>
                </div>
                
                <div class="form-grupo">
                    <label>
                        <input type="checkbox" name="destaque" id="editar-destaque"> Destacar na página inicial
                    </label>
                </div>
                
                <div class="modal-botoes">
                    <button type="button" class="modal-btn secondary" onclick="fecharModal()">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-btn primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Confirmação de Exclusão -->
    <div id="modal-excluir" class="modal">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h2>
                <button class="modal-fechar" onclick="fecharModal()">&times;</button>
            </div>
            
            <p>Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.</p>
            
            <form method="POST" action="">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id" id="excluir-id">
                
                <div class="modal-botoes">
                    <button type="button" class="modal-btn secondary" onclick="fecharModal()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="modal-btn primary">
                        <i class="fas fa-trash"></i> Confirmar Exclusão
                    </button>
                </div>
            </form>
        </div>
    </div>

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
    // Abre o modal de adicionar e reseta os campos
    function abrirModalAdicionar() {
        document.getElementById('adicionar-titulo').value = '';
        document.getElementById('adicionar-tipo').value = 'filme';
        document.getElementById('adicionar-capa').value = '';
        document.getElementById('adicionar-ano').value = new Date().getFullYear();
        document.getElementById('adicionar-categoria').value = '';
        document.getElementById('adicionar-descricao').value = '';
        document.getElementById('adicionar-destaque').checked = false;
        document.getElementById('preview-capa-adicionar').style.display = 'none';

        const modal = document.getElementById('modal-adicionar');
        modal.style.display = 'flex';
        document.body.classList.add('modal-aberto');
    }

    // Abre o modal de editar e preenche os campos
    function abrirModalEditar(id, titulo, tipo, capa, ano, categoria, descricao, destaque) {
        document.getElementById('editar-id').value = id;
        document.getElementById('editar-titulo').value = titulo;
        document.getElementById('editar-tipo').value = tipo;
        document.getElementById('capa-atual').value = capa;
        document.getElementById('editar-ano').value = ano;
        document.getElementById('editar-categoria').value = categoria;
        document.getElementById('editar-descricao').value = descricao;
        document.getElementById('editar-destaque').checked = destaque == 1;

        const preview = document.getElementById('preview-capa-editar');
        if (capa && capa !== '') {
            preview.src = capa;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }

        const modal = document.getElementById('modal-editar');
        modal.style.display = 'flex';
        document.body.classList.add('modal-aberto');
    }

    // Abre o modal de confirmação de exclusão
    function confirmarExclusao(id) {
        document.getElementById('excluir-id').value = id;
        document.getElementById('modal-excluir').style.display = 'flex';
        document.body.classList.add('modal-aberto');
    }

    // Fecha todos os modais e reativa a rolagem do body
    function fecharTodosModais() {
        document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
        document.body.classList.remove('modal-aberto');
    }

    // Fecha o modal de troca de foto
    function fecharModalFoto() {
        document.getElementById('modal-foto').style.display = 'none';
        document.body.classList.remove('modal-aberto');
    }

    // Fecha ao clicar fora do conteúdo do modal
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            fecharTodosModais();
        }
    });

    // Input de busca com debounce
    const buscaInput = document.querySelector('.busca input');
    let buscaTimeout;

    if (buscaInput) {
        buscaInput.addEventListener('input', function () {
            clearTimeout(buscaTimeout);
            buscaTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

            // Dropdown do avatar - Versão melhorada
        function toggleDropdown(event) {
            event.stopPropagation(); // Impede a propagação do evento
            const usuario = document.querySelector('.usuario');
            const dropdown = usuario.querySelector('.dropdown-usuario');
            
            // Fecha todos os outros dropdowns abertos
            document.querySelectorAll('.usuario').forEach(u => {
                if (u !== usuario) {
                    u.classList.remove('active');
                }
            });
            
            // Alterna o estado do dropdown atual
            usuario.classList.toggle('active');
            
            // Se estiver abrindo, posiciona corretamente
            if (usuario.classList.contains('active')) {
                const rect = usuario.getBoundingClientRect();
                dropdown.style.top = `${rect.height}px`;
                dropdown.style.right = '0';
            }
        }

        // Fecha o dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.usuario')) {
                document.querySelectorAll('.usuario').forEach(u => {
                    u.classList.remove('active');
                });
            }
        });

        // Modal de mudança de foto - Versão melhorada
        function abrirModalMudarFoto(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Fecha o dropdown primeiro
            document.querySelector('.usuario').classList.remove('active');
            
            // Abre o modal
            document.getElementById('modal-foto').style.display = 'flex';
            document.body.classList.add('modal-aberto');
        }

    // Preview de imagem ao selecionar nova foto
    document.getElementById('nova-foto').addEventListener('change', function (e) {
        const preview = document.getElementById('preview-foto');
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });

            // Verifica se a foto do usuário foi carregada corretamente
        document.addEventListener('DOMContentLoaded', function() {
            const userPhoto = document.querySelector('.usuario img');
            userPhoto.onerror = function() {
                this.src = 'img/avatar-padrao.jpg'; // Fallback caso a foto não carregue
            };
            
            // Força o recarregamento da foto após mudança
            if (window.performance && performance.navigation.type === 1) {
                // Se a página foi recarregada, adiciona um timestamp à URL da foto
                // para evitar cache do navegador
                if (userPhoto.src.indexOf('?') === -1) {
                    userPhoto.src = userPhoto.src + '?t=' + new Date().getTime();
                } else {
                    userPhoto.src = userPhoto.src.split('?')[0] + '?t=' + new Date().getTime();
                }
            }
        });

    // Preview de capa - adicionar
    document.getElementById('adicionar-capa').addEventListener('change', function (e) {
        const preview = document.getElementById('preview-capa-adicionar');
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });

    // Preview de capa - editar
    document.getElementById('editar-capa').addEventListener('change', function (e) {
        const preview = document.getElementById('preview-capa-editar');
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });

    // Atualiza o destaque do item
    function toggleDestaque(id, elemento) {
        fetch('atualizar_destaque.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                elemento.classList.toggle('ativo');
                mostrarMensagem(`Item ${data.destaque ? 'destacado' : 'removido dos destaques'} com sucesso!`, 'sucesso');
            } else {
                throw new Error(data.message || 'Erro ao atualizar destaque');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem(error.message, 'erro');
        });
    }

    // Exibe mensagens de alerta
    function mostrarMensagem(mensagem, tipo) {
        const divMensagem = document.createElement('div');
        divMensagem.className = `mensagem ${tipo}`;
        divMensagem.innerHTML = `<i class="fas fa-${tipo === 'sucesso' ? 'check' : 'exclamation'}-circle"></i> ${mensagem}`;
        document.querySelector('main').prepend(divMensagem);

        setTimeout(() => {
            divMensagem.remove();
        }, 5000);
    }
</script>

</body>
</html>
