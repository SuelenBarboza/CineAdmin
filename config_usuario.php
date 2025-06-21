<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

$paginaTitulo = "Minhas Configurações";

// Carregar configurações do usuário
$configuracoes = [];

$mysqli = conexao();
if ($mysqli->connect_error) {
    error_log("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
} else {
    try {
        $usuario_id = $_SESSION['usuario_id'];
        $query = "SELECT * FROM configuracoes_usuario WHERE usuario_id = ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $configuracoes = $result->fetch_assoc();
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Erro ao carregar configurações: " . $e->getMessage());
    }
}

// Valores padrão com modo escuro ativado
$configuracoes = array_merge([
    'modo_escuro' => true,
    'tema_cor' => '#e50914',
    'fonte' => "'Sans', 'Helvetica Neue', Arial, sans-serif",
    'tamanho_fonte' => '16px',
    'notificacoes_geral' => true,
    'notificacoes_novos_conteudos' => true,
    'notificacoes_recomendacoes' => true,
    'frequencia_notificacoes' => 'diario',
    'privacidade_perfil' => 'publico',
    'privacidade_lista' => 'amigos',
    'autenticacao_2fatores' => false,
    'receber_emails' => true,
    'idioma' => 'pt_BR',
    'player_qualidade' => 'auto'
], $configuracoes);

// Determinar classe CSS baseada no modo escuro
$modoEscuroClass = $configuracoes['modo_escuro'] ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="pt-BR" class="<?= $modoEscuroClass ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($paginaTitulo) ?> - CineUser</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --cor-destaque: <?= $configuracoes['tema_cor'] ?>;
            --fonte-selecionada: <?= $configuracoes['fonte'] ?>;
            --tamanho-fonte-selecionado: <?= $configuracoes['tamanho_fonte'] ?>;
        }
    </style>
    <link rel="stylesheet" href="css/config_usuario.css">
</head>
<body>
    <?php include 'header_usuario.php'; ?>
    
    <main class="conteudo">
        <div class="config-container">
            <h1 class="config-titulo">
                <i class="fas fa-user-cog"></i>
                Minhas Configurações
            </h1>
            
            <div class="config-grid">
                <!-- Menu Lateral -->
                <div class="menu-links">
                    <a href="#" class="menu-link ativo" data-secao="aparencia">
                        <i class="fas fa-palette"></i> Aparência
                    </a>
                    <a href="#" class="menu-link" data-secao="conta">
                        <i class="fas fa-user"></i> Minha Conta
                    </a>
                    <a href="#" class="menu-link" data-secao="notificacoes">
                        <i class="fas fa-bell"></i> Notificações
                    </a>
                    <a href="#" class="menu-link" data-secao="privacidade">
                        <i class="fas fa-lock"></i> Privacidade
                    </a>
                    <a href="#" class="menu-link" data-secao="player">
                        <i class="fas fa-play-circle"></i> Player
                    </a>
                </div>
                
                <!-- Conteúdo Principal -->
                <div class="config-conteudo">
                    <!-- Seção Aparência -->
                    <section id="aparencia" class="config-secao ativo">
                        <h2><i class="fas fa-palette"></i> Aparência</h2>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Modo Escuro</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="modo-escuro" <?= $configuracoes['modo_escuro'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="tema-cor">Cor do Tema</label>
                            <select id="tema-cor" class="form-control">
                                <option value="#e50914" <?= $configuracoes['tema_cor'] == '#e50914' ? 'selected' : '' ?>>Vermelho (Padrão)</option>
                                <option value="#007bff" <?= $configuracoes['tema_cor'] == '#007bff' ? 'selected' : '' ?>>Azul</option>
                                <option value="#28a745" <?= $configuracoes['tema_cor'] == '#28a745' ? 'selected' : '' ?>>Verde</option>
                                <option value="#6f42c1" <?= $configuracoes['tema_cor'] == '#6f42c1' ? 'selected' : '' ?>>Roxo</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="fonte">Fonte</label>
                            <select id="fonte" class="form-control">
                                <option value="'Sans', 'Helvetica Neue', Arial, sans-serif" <?= $configuracoes['fonte'] === "'Sans', 'Helvetica Neue', Arial, sans-serif" ? 'selected' : '' ?>> Sans</option>
                                <option value="Arial, sans-serif" <?= $configuracoes['fonte'] === "Arial, sans-serif" ? 'selected' : '' ?>>Arial</option>
                                <option value="'Roboto', sans-serif" <?= $configuracoes['fonte'] === "'Roboto', sans-serif" ? 'selected' : '' ?>>Roboto</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="tamanho-fonte">Tamanho da Fonte</label>
                            <select id="tamanho-fonte" class="form-control">
                                <option value="14px" <?= $configuracoes['tamanho_fonte'] === "14px" ? 'selected' : '' ?>>Pequeno</option>
                                <option value="16px" <?= $configuracoes['tamanho_fonte'] === "16px" ? 'selected' : '' ?>>Médio</option>
                                <option value="18px" <?= $configuracoes['tamanho_fonte'] === "18px" ? 'selected' : '' ?>>Grande</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="aparencia">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Conta -->
                    <section id="conta" class="config-secao">
                        <h2><i class="fas fa-user"></i> Minha Conta</h2>
                        
                        <div class="form-grupo">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" class="form-control" value="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>" placeholder="Seu nome">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($_SESSION['usuario_email'] ?? '') ?>" placeholder="Seu e-mail">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="usuario">Nome de Usuário</label>
                            <input type="text" id="usuario" class="form-control" value="<?= htmlspecialchars($_SESSION['usuario_usuario'] ?? '') ?>" placeholder="Nome de usuário">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="foto-perfil">Foto de Perfil</label>
                            <input type="file" id="foto-perfil" class="form-control" accept="image/*">
                            <p class="form-texto-ajuda">Tamanho máximo: 2MB. Formatos: JPG, PNG</p>
                        </div>
                        
                        <div class="form-grupo">
                            <button id="btn-alterar-senha" class="btn-secundario">
                                <i class="fas fa-key"></i> Alterar Senha
                            </button>
                        </div>
                        
                        <button class="btn-salvar" data-secao="conta">
                            <i class="fas fa-save"></i> Atualizar Conta
                        </button>
                    </section>
                    
                    <!-- Seção Notificações -->
                    <section id="notificacoes" class="config-secao">
                        <h2><i class="fas fa-bell"></i> Notificações</h2>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Ativar Notificações</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notificacoes-geral" <?= $configuracoes['notificacoes_geral'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="form-grupo">
                            <label>Tipos de Notificação</label>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Novos Conteúdos</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-novos-conteudos" <?= $configuracoes['notificacoes_novos_conteudos'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                                <p class="form-texto-ajuda">Receber notificações quando novos filmes/séries são adicionados</p>
                            </div>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Recomendações</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-recomendacoes" <?= $configuracoes['notificacoes_recomendacoes'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                                <p class="form-texto-ajuda">Receber recomendações personalizadas baseadas em seus gostos</p>
                            </div>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="frequencia-notificacoes">Frequência de Notificações</label>
                            <select id="frequencia-notificacoes" class="form-control">
                                <option value="instantaneo" <?= $configuracoes['frequencia_notificacoes'] == "instantaneo" ? 'selected' : '' ?>>Instantâneo</option>
                                <option value="diario" <?= $configuracoes['frequencia_notificacoes'] == "diario" ? 'selected' : '' ?>>Resumo Diário</option>
                                <option value="semanal" <?= $configuracoes['frequencia_notificacoes'] == "semanal" ? 'selected' : '' ?>>Resumo Semanal</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Receber E-mails</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="receber-emails" <?= $configuracoes['receber_emails'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Receber notificações por e-mail</p>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="notificacoes">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Privacidade -->
                    <section id="privacidade" class="config-secao">
                        <h2><i class="fas fa-lock"></i> Privacidade</h2>
                        
                        <div class="form-grupo">
                            <label for="privacidade-perfil">Visibilidade do Perfil</label>
                            <select id="privacidade-perfil" class="form-control">
                                <option value="publico" <?= $configuracoes['privacidade_perfil'] == "publico" ? 'selected' : '' ?>>Público</option>
                                <option value="amigos" <?= $configuracoes['privacidade_perfil'] == "amigos" ? 'selected' : '' ?>>Somente Amigos</option>
                                <option value="privado" <?= $configuracoes['privacidade_perfil'] == "privado" ? 'selected' : '' ?>>Privado</option>
                            </select>
                            <p class="form-texto-ajuda">Quem pode ver seu perfil e atividades</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="privacidade-lista">Visibilidade da Minha Lista</label>
                            <select id="privacidade-lista" class="form-control">
                                <option value="publico" <?= $configuracoes['privacidade_lista'] == "publico" ? 'selected' : '' ?>>Público</option>
                                <option value="amigos" <?= $configuracoes['privacidade_lista'] == "amigos" ? 'selected' : '' ?>>Somente Amigos</option>
                                <option value="privado" <?= $configuracoes['privacidade_lista'] == "privado" ? 'selected' : '' ?>>Privado</option>
                            </select>
                            <p class="form-texto-ajuda">Quem pode ver sua lista de filmes e séries</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Autenticação em Dois Fatores</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="autenticacao-2fatores" <?= $configuracoes['autenticacao_2fatores'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Requer um código adicional enviado por e-mail ou app ao fazer login</p>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="privacidade">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Player -->
                    <section id="player" class="config-secao">
                        <h2><i class="fas fa-play-circle"></i> Configurações do Player</h2>
                        
                        <div class="form-grupo">
                            <label for="player-qualidade">Qualidade Padrão</label>
                            <select id="player-qualidade" class="form-control">
                                <option value="auto" <?= $configuracoes['player_qualidade'] == "auto" ? 'selected' : '' ?>>Automática</option>
                                <option value="1080" <?= $configuracoes['player_qualidade'] == "1080" ? 'selected' : '' ?>>1080p (Full HD)</option>
                                <option value="720" <?= $configuracoes['player_qualidade'] == "720" ? 'selected' : '' ?>>720p (HD)</option>
                                <option value="480" <?= $configuracoes['player_qualidade'] == "480" ? 'selected' : '' ?>>480p</option>
                            </select>
                            <p class="form-texto-ajuda">A qualidade real pode variar conforme sua conexão</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Reprodução Automática</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="player-autoplay" checked>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Reproduzir automaticamente o próximo episódio</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Legendas Automáticas</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="player-legendas" checked>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Ativar legendas quando disponíveis</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="player-idioma">Idioma Preferido</label>
                            <select id="player-idioma" class="form-control">
                                <option value="pt_BR" <?= $configuracoes['idioma'] == "pt_BR" ? 'selected' : '' ?>>Português (Brasil)</option>
                                <option value="pt_PT" <?= $configuracoes['idioma'] == "pt_PT" ? 'selected' : '' ?>>Português (Portugal)</option>
                                <option value="en_US" <?= $configuracoes['idioma'] == "en_US" ? 'selected' : '' ?>>Inglês</option>
                                <option value="es_ES" <?= $configuracoes['idioma'] == "es_ES" ? 'selected' : '' ?>>Espanhol</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="player">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                </div>
            </div>
        </div>
    </main>

   
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".menu-link");
    const secoes = document.querySelectorAll(".config-secao");
    const botaoTema = document.getElementById("btn-tema");
    const body = document.body;

    // Verifica e aplica tema salvo no localStorage
    const temaSalvo = localStorage.getItem("tema");
    if (temaSalvo === "dark") {
        body.classList.add("dark-mode");
        if (botaoTema) botaoTema.textContent = "☀️";
    }

    // Alternar abas ao clicar
    links.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            links.forEach(l => l.classList.remove("ativo"));
            secoes.forEach(secao => secao.classList.remove("ativo"));

            this.classList.add("ativo");
            const idSecao = this.getAttribute("data-secao");
            const secaoAtiva = document.getElementById(idSecao);

            if (secaoAtiva) {
                secaoAtiva.classList.add("ativo");
            }
        });
    });

    // Alternar tema claro/escuro
    if (botaoTema) {
        botaoTema.addEventListener("click", function () {
            body.classList.toggle("dark-mode");

            const modoEscuroAtivo = body.classList.contains("dark-mode");
            botaoTema.textContent = modoEscuroAtivo ? "☀️" : "🌙";
            localStorage.setItem("tema", modoEscuroAtivo ? "dark" : "light");
        });
    }
});

</script>

</body>
</html>