<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$mysqli = conexao();
$query = "SELECT c.* FROM lista_usuarios l 
          JOIN catalogo c ON l.conteudo_id = c.id 
          WHERE l.usuario_id = ? 
          ORDER BY l.data_adicao DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$minha_lista = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Lista - CineAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/minha_lista.css">
</head>

<body>

    <header>
        <?php include 'header_usuario.php'; ?>

    </header>

    <main class="conteudo">
        <div class="catalogo-container" id="minha-lista">
            <h2 class="catalogo-titulo">
                <i class="fas fa-list"></i> Minha Lista
            </h2>

            <div id="toast-notification" class="toast-notification">
                <i class="fas fa-check-circle"></i>
                <span>Item excluído com sucesso!</span>
            </div>

            <?php if (!empty($minha_lista)): ?>
                <?php foreach ($minha_lista as $item): ?>
                    <div class="item">
                        <img src="<?= !empty($item['capa']) ? $item['capa'] : 'img/sem-imagem.jpg' ?>"
                            alt="<?= htmlspecialchars($item['titulo'] ?? '') ?>" onerror="this.src='img/sem-imagem.jpg'">
                        <span class="tipo"><?= strtoupper($item['tipo'] ?? '') ?></span>

                        
                        <div class="acoes-overlay">
                            <a href="detalhes_usuario.php?id=<?= $item['id'] ?>" class="acao-btn" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="acao-btn" onclick="toggleLista(<?= $item['id'] ?>, this)" title="Remover da lista">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="lista-vazia">Sua lista está vazia. Adicione filmes e séries para vê-los aqui.</p>
            <?php endif; ?>
        </div>
    </main>

        


    <script>
        function toggleLista(conteudoId, botao) {
            fetch('manipular_lista.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `acao=remover&conteudo_id=${conteudoId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        botao.closest('.item').remove();
                        if (document.querySelectorAll('.catalogo-container .item').length === 0) {
                            const catalogo = document.querySelector('.catalogo-container');
                            catalogo.insertAdjacentHTML('afterend', '<p class="lista-vazia">Sua lista está vazia. Adicione filmes e séries para vê-los aqui.</p>');
                            catalogo.remove();
                        }
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar sua solicitação');
                });
            function abrirModal() {
                const modal = document.getElementById('modal-removido');
                modal.style.display = 'flex';

                
                setTimeout(() => {
                    fecharModal();
                }, 3000);
            }

            function fecharModal() {
                document.getElementById('modal-removido').style.display = 'none';
            }

            
            function removerDaLista(conteudoId) {
                fetch('manipular_lista.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `acao=remover&conteudo_id=${conteudoId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            abrirModal(); 
                            
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        alert("Erro ao processar a solicitação.");
                        console.error(error);
                    });
            }
        }


       
    
    function toggleLista(conteudoId, botao) {
        fetch('manipular_lista.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `acao=remover&conteudo_id=${conteudoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Animação de remoção
                const item = botao.closest('.item');
                item.classList.add('item-removing');
                
                // Remove após a animação
                setTimeout(() => {
                    item.remove();
                    
                    // Verifica se a lista ficou vazia
                    if (document.querySelectorAll('.catalogo-container .item').length === 0) {
                        const catalogo = document.querySelector('.catalogo-container');
                        const mensagem = document.createElement('p');
                        mensagem.className = 'lista-vazia';
                        mensagem.textContent = 'Sua lista está vazia. Adicione filmes e séries para vê-los aqui.';
                        catalogo.parentNode.insertBefore(mensagem, catalogo.nextSibling);
                        catalogo.remove();
                    }
                }, 300);
                
                // Mostra notificação centralizada
                const toast = document.getElementById('toast-notification');
                toast.classList.add('show');
                
                // Esconde após 1.5 segundos
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 1700);
                
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar sua solicitação');
        });
    }

</script>
</body>
</html>