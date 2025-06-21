<?php
session_start();
require_once 'conexao.php';


$status_disponiveis = [
    'aberto' => ['label' => 'Aberto', 'cor' => '#e50914', 'icone' => 'fa-exclamation-circle'],
    'em_analise' => ['label' => 'Em Análise', 'cor' => '#ffc107', 'icone' => 'fa-search'],
    'em_andamento' => ['label' => 'Em Andamento', 'cor' => '#17a2b8', 'icone' => 'fa-spinner'],
    'resolvido' => ['label' => 'Resolvido', 'cor' => '#28a745', 'icone' => 'fa-check-circle'],
    'nao_resolvido' => ['label' => 'Não Resolvido', 'cor' => '#6c757d', 'icone' => 'fa-times-circle'],
    'fechado' => ['label' => 'Fechado', 'cor' => '#343a40', 'icone' => 'fa-lock']
];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin_logado'])) {
    if (isset($_POST['ticket_id']) && isset($_POST['novo_status']) && array_key_exists($_POST['novo_status'], $status_disponiveis)) {
        $ticket_id = $_POST['ticket_id'];
        $novo_status = $_POST['novo_status'];
        $comentario = trim($_POST['comentario'] ?? '');
        
        $mysqli = conexao();
        
        try {
            
            $mysqli->begin_transaction();
            
            
            $stmt = $mysqli->prepare("UPDATE tickets_suporte SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $novo_status, $ticket_id);
            $stmt->execute();
            
            
            $stmt_historico = $mysqli->prepare("INSERT INTO tickets_historico 
                (ticket_id, usuario_id, acao, comentario) 
                VALUES (?, ?, ?, ?)");
            $acao = "Status alterado para: " . $status_disponiveis[$novo_status]['label'];
            $stmt_historico->bind_param("iiss", $ticket_id, $_SESSION['admin_id'], $acao, $comentario);
            $stmt_historico->execute();
            
            
            $mysqli->commit();
            
            $_SESSION['sucesso_admin'] = "Status do chamado atualizado com sucesso!";
        } catch (Exception $e) {
            
            $mysqli->rollback();
            $_SESSION['erro_admin'] = "Erro ao atualizar o chamado: " . $e->getMessage();
        }
        
        header("Location: suporte_admin.php");
        exit();
    }
}


$pagina = max(1, $_GET['pagina'] ?? 1);
$itens_por_pagina = 10;
$offset = ($pagina - 1) * $itens_por_pagina;

$mysqli = conexao();


$total_tickets = $mysqli->query("SELECT COUNT(*) as total FROM tickets_suporte")->fetch_assoc()['total'];
$total_paginas = ceil($total_tickets / $itens_por_pagina);


$query = "SELECT t.*, u.nome as usuario_nome, u.email as usuario_email 
          FROM tickets_suporte t
          JOIN usuarios u ON t.usuario_id = u.id
          ORDER BY t.data_abertura DESC
          LIMIT $itens_por_pagina OFFSET $offset";
$result = $mysqli->query($query);
$tickets = $result->fetch_all(MYSQLI_ASSOC);


$historico_tickets = [];
if (isset($_SESSION['admin_logado'])) {
    foreach ($tickets as $ticket) {
        $query_historico = "SELECT th.*, u.nome as admin_nome 
                           FROM tickets_historico th
                           LEFT JOIN administradores u ON th.usuario_id = u.id
                           WHERE th.ticket_id = ?
                           ORDER BY th.data_acao DESC";
        $stmt = $mysqli->prepare($query_historico);
        $stmt->bind_param("i", $ticket['id']);
        $stmt->execute();
        $result_historico = $stmt->get_result();
        $historico_tickets[$ticket['id']] = $result_historico->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Suporte - CineAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos do seu cabeçalho existente */
        .cabecalho {
            background-color: #141414;
            padding: 15px 4%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 92%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        
        .logo {
            color: #e50914;
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Estilos do conteúdo principal */
        .conteudo {
            padding-top: 80px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px 20px;
        }
        
        .suporte-container {
            background-color: #1a1a1a;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.3);
        }
        
        .suporte-titulo {
            color: #e50914;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tickets-list {
            display: grid;
            gap: 15px;
        }
        
        .ticket-item {
            background-color: #2d2d2d;
            border-radius: 6px;
            padding: 15px;
            border-left: 4px solid;
        }
        
        .ticket-header {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .ticket-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #ddd;
        }
        
        .ticket-title {
            font-weight: bold;
            color: #fff;
            margin: 5px 0;
        }
        
        .ticket-status {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .ticket-message {
            margin: 10px 0;
            padding: 10px;
            background-color: rgba(0,0,0,0.3);
            border-radius: 4px;
            white-space: pre-wrap;
        }
        
        .status-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }
        
        .status-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .status-btn:hover {
            transform: translateY(-2px);
        }
        
        .comentario-area {
            margin-top: 10px;
            width: 100%;
        }
        
        .comentario-area textarea {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #444;
            background-color: #333;
            color: #fff;
            resize: vertical;
            min-height: 80px;
        }
        
        .historico-ticket {
            margin-top: 20px;
            border-top: 1px solid #444;
            padding-top: 15px;
        }
        
        .item-historico {
            background-color: #333;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .item-historico .meta {
            font-size: 0.8rem;
            color: #aaa;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .item-historico .comentario {
            padding: 5px;
            background-color: rgba(0,0,0,0.3);
            border-radius: 3px;
        }
        
        .paginacao {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        
        .pagina-btn {
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .pagina-btn:hover, .pagina-btn.ativa {
            background-color: #e50914;
        }
        
        .mensagem-flutuante {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.3s;
        }
        
        .sucesso {
            background-color: #28a745;
            color: white;
        }
        
        .erro {
            background-color: #dc3545;
            color: white;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    
    <header>
        <?php include 'header_admin.php'; ?>  
    </header>

    <main class="conteudo">
        <div class="suporte-container">
            <h1 class="suporte-titulo">
                <i class="fas fa-headset"></i> Central de Suporte
                <span style="font-size: 1rem; color: #aaa; margin-left: auto;">
                    Total de chamados: <?= $total_tickets ?>
                </span>
            </h1>

            <?php if (isset($_SESSION['sucesso_admin'])): ?>
                <div class="mensagem-flutuante sucesso">
                    <i class="fas fa-check-circle"></i>
                    <span><?= $_SESSION['sucesso_admin'] ?></span>
                </div>
                <?php unset($_SESSION['sucesso_admin']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro_admin'])): ?>
                <div class="mensagem-flutuante erro">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= $_SESSION['erro_admin'] ?></span>
                </div>
                <?php unset($_SESSION['erro_admin']); ?>
            <?php endif; ?>

            <div class="tickets-list">
                <?php if (empty($tickets)): ?>
                    <div class="ticket-item" style="text-align: center; padding: 30px;">
                        <i class="fas fa-inbox" style="font-size: 2rem; color: #666; margin-bottom: 10px;"></i>
                        <p>Nenhum chamado encontrado</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <?php 
                            // Verificação para garantir que a chave do status exista no array $status_disponiveis
                            $status = isset($status_disponiveis[$ticket['status']]) ? $status_disponiveis[$ticket['status']] : null;
                        ?>
                        <div class="ticket-item" style="border-left-color: <?= $status ? $status['cor'] : '#ccc' ?>">
                            <div class="ticket-header">
                                <div>
                                    <div class="ticket-user">
                                        <i class="fas fa-user"></i>
                                        <span><?= htmlspecialchars($ticket['usuario_nome']) ?> (<?= htmlspecialchars($ticket['usuario_email']) ?>)</span>
                                    </div>
                                    <h3 class="ticket-title"><?= htmlspecialchars($ticket['assunto']) ?></h3>
                                </div>
                                <div>
                                    <span class="ticket-status" style="background-color: <?= $status ? $status['cor'] . '20' : '#ccc' ?>; color: <?= $status ? $status['cor'] : '#ccc' ?>">
                                        <i class="fas <?= $status ? $status['icone'] : 'fa-question-circle' ?>"></i>
                                        <?= $status ? $status['label'] : 'Desconhecido' ?>
                                    </span>
                                    <div style="text-align: right; font-size: 0.8rem; color: #aaa;">
                                        <?= date('d/m/Y H:i', strtotime($ticket['data_abertura'])) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ticket-message">
                                <?= nl2br(htmlspecialchars($ticket['mensagem'])) ?>
                            </div>
                            
                            <?php if (isset($_SESSION['admin_logado'])): ?>
                                <form method="POST">
                                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                    
                                    <div class="status-options">
                                        <?php foreach ($status_disponiveis as $status_key => $dados): ?>
                                            <?php if ($status_key !== $ticket['status']): ?>
                                                <button type="submit" name="novo_status" value="<?= $status_key ?>" class="status-btn" style="background-color: <?= $dados['cor'] ?>20; color: <?= $dados['cor'] ?>">
                                                    <i class="fas <?= $dados['icone'] ?>"></i>
                                                    <?= $dados['label'] ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="comentario-area">
                                        <textarea name="comentario" placeholder="Adicione um comentário (opcional)"></textarea>
                                    </div>
                                </form>
                                
                                <?php if (!empty($historico_tickets[$ticket['id']])): ?>
                                    <div class="historico-ticket">
                                        <h4><i class="fas fa-history"></i> Histórico</h4>
                                        <?php foreach ($historico_tickets[$ticket['id']] as $item): ?>
                                            <div class="item-historico">
                                                <div class="meta">
                                                    <span><?= date('d/m/Y H:i', strtotime($item['data_acao'])) ?></span>
                                                    <span><?= htmlspecialchars($item['admin_nome'] ?? 'Sistema') ?></span>
                                                </div>
                                                <div><strong><?= htmlspecialchars($item['acao']) ?></strong></div>
                                                <?php if (!empty($item['comentario'])): ?>
                                                    <div class="comentario"><?= nl2br(htmlspecialchars($item['comentario'])) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_paginas > 1): ?>
                <div class="paginacao">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?= $i ?>" class="pagina-btn <?= $i == $pagina ? 'ativa' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        setTimeout(() => {
            const mensagens = document.querySelectorAll('.mensagem-flutuante');
            mensagens.forEach(msg => {
                msg.style.animation = 'fadeIn 0.3s reverse';
                setTimeout(() => msg.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
