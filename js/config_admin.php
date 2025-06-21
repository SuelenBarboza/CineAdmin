<?php 
session_start();
require_once 'conexao.php';


if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$paginaTitulo = "Configurações";
include 'header_admin.php'; 

// Carregar configurações do banco de dados
try {
    $mysqli = conexao();
    $stmt = $pdo->prepare("SELECT * FROM configuracoes_sistema WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $stmt->execute();
    
    $configuracoes = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $configuracoes = [];
    error_log("Erro ao carregar configurações: " . $e->getMessage());
}
?>

<main class="conteudo">
    <div class="config-container">
        <h1 class="config-titulo">
            <i class="fas fa-cog"></i>
            Configurações do Sistema
        </h1>
        
        <div class="config-grid">
            <!-- Menu Lateral -->
            <div class="config-menu">
                <h3>Menu de Configurações</h3>
                <div class="menu-links">
                    <a href="#aparencia" class="menu-link ativo">
                        <i class="fas fa-palette"></i> Aparência
                    </a>
                    <a href="#conta" class="menu-link">
                        <i class="fas fa-user-shield"></i> Conta
                    </a>
                    <a href="#notificacoes" class="menu-link">
                        <i class="fas fa-bell"></i> Notificações
                    </a>
                    <a href="#seguranca" class="menu-link">
                        <i class="fas fa-lock"></i> Segurança
                    </a>
                    <a href="#usuarios" class="menu-link">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                    <a href="#sistema" class="menu-link">
                        <i class="fas fa-server"></i> Sistema
                    </a>
                    <a href="#backup" class="menu-link">
                        <i class="fas fa-database"></i> Backup
                    </a>
                </div>
            </div>
            
            <!-- Conteúdo Principal -->
            <div class="config-conteudo">
                <!-- Seção Aparência -->
                <section id="aparencia" class="config-secao">
                    <h2><i class="fas fa-palette"></i> Aparência</h2>
                    
                    <div class="form-grupo">
                        <label class="toggle-label">
                            <span class="toggle-text">Modo Escuro</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="modo-escuro" <?= isset($configuracoes['modo_escuro']) && $configuracoes['modo_escuro'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </label>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="tema-cor">Cor do Tema</label>
                        <select id="tema-cor" class="form-control">
                            <option value="#e50914" <?= (!isset($configuracoes['tema_cor'])) || $configuracoes['tema_cor'] == '#e50914' ? 'selected' : '' ?>>Vermelho (Padrão)</option>
                            <option value="#007bff" <?= isset($configuracoes['tema_cor']) && $configuracoes['tema_cor'] == '#007bff' ? 'selected' : '' ?>>Azul</option>
                            <option value="#28a745" <?= isset($configuracoes['tema_cor']) && $configuracoes['tema_cor'] == '#28a745' ? 'selected' : '' ?>>Verde</option>
                            <option value="#6f42c1" <?= isset($configuracoes['tema_cor']) && $configuracoes['tema_cor'] == '#6f42c1' ? 'selected' : '' ?>>Roxo</option>
                            <option value="#fd7e14" <?= isset($configuracoes['tema_cor']) && $configuracoes['tema_cor'] == '#fd7e14' ? 'selected' : '' ?>>Laranja</option>
                            <option value="#17a2b8" <?= isset($configuracoes['tema_cor']) && $configuracoes['tema_cor'] == '#17a2b8' ? 'selected' : '' ?>>Ciano</option>
                        </select>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="fonte">Fonte do Sistema</label>
                        <select id="fonte" class="form-control">
                            <option value="'Netflix Sans', 'Helvetica Neue', Arial, sans-serif" <?= (!isset($configuracoes['fonte'])) || $configuracoes['fonte'] == "'Netflix Sans', 'Helvetica Neue', Arial, sans-serif" ? 'selected' : '' ?>>Netflix Sans (Padrão)</option>
                            <option value="Arial, sans-serif" <?= isset($configuracoes['fonte']) && $configuracoes['fonte'] == "Arial, sans-serif" ? 'selected' : '' ?>>Arial</option>
                            <option value="'Roboto', sans-serif" <?= isset($configuracoes['fonte']) && $configuracoes['fonte'] == "'Roboto', sans-serif" ? 'selected' : '' ?>>Roboto</option>
                            <option value="'Open Sans', sans-serif" <?= isset($configuracoes['fonte']) && $configuracoes['fonte'] == "'Open Sans', sans-serif" ? 'selected' : '' ?>>Open Sans</option>
                            <option value="'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" <?= isset($configuracoes['fonte']) && $configuracoes['fonte'] == "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" ? 'selected' : '' ?>>Segoe UI</option>
                        </select>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="tamanho-fonte">Tamanho da Fonte</label>
                        <select id="tamanho-fonte" class="form-control">
                            <option value="14px" <?= isset($configuracoes['tamanho_fonte']) && $configuracoes['tamanho_fonte'] == "14px" ? 'selected' : '' ?>>Pequeno</option>
                            <option value="16px" <?= (!isset($configuracoes['tamanho_fonte'])) || $configuracoes['tamanho_fonte'] == "16px" ? 'selected' : '' ?>>Médio (Padrão)</option>
                            <option value="18px" <?= isset($configuracoes['tamanho_fonte']) && $configuracoes['tamanho_fonte'] == "18px" ? 'selected' : '' ?>>Grande</option>
                        </select>
                    </div>
                    
                    <button class="btn-salvar" data-secao="aparencia">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </section>
                
                <!-- Seção Conta -->
                <section id="conta" class="config-secao" style="display: none;">
                    <h2><i class="fas fa-user-shield"></i> Configurações da Conta</h2>
                    
                    <div class="form-grupo">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" value="<?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador') ?>" placeholder="Seu nome completo">
                    </div>
                    
                    <div class="form-grupo">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@cineadmin.com') ?>" placeholder="Seu e-mail">
                    </div>
                    
                    <div class="form-grupo">
                        <label for="usuario">Nome de Usuário</label>
                        <input type="text" id="usuario" value="<?= htmlspecialchars($_SESSION['admin_usuario'] ?? 'admin') ?>" placeholder="Nome de usuário">
                    </div>
                    
                    <div class="form-grupo">
                        <label for="foto-perfil">Foto de Perfil</label>
                        <input type="file" id="foto-perfil" accept="image/*">
                    </div>
                    
                    <button class="btn-salvar" data-secao="conta">
                        <i class="fas fa-save"></i> Atualizar Conta
                    </button>
                </section>
                
                <!-- Seção Notificações -->
                <section id="notificacoes" class="config-secao" style="display: none;">
                    <h2><i class="fas fa-bell"></i> Notificações</h2>
                    
                    <div class="form-grupo">
                        <label class="toggle-label">
                            <span class="toggle-text">Ativar Notificações</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="notificacoes-geral" <?= isset($configuracoes['notificacoes_geral']) && $configuracoes['notificacoes_geral'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </label>
                    </div>
                    
                    <div class="form-grupo">
                        <label>Tipos de Notificação</label>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Novos Usuários</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notificacoes-usuarios" <?= isset($configuracoes['notificacoes_usuarios']) && $configuracoes['notificacoes_usuarios'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Atualizações do Sistema</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notificacoes-atualizacoes" <?= isset($configuracoes['notificacoes_atualizacoes']) && $configuracoes['notificacoes_atualizacoes'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Backups Automáticos</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notificacoes-backups" <?= isset($configuracoes['notificacoes_backups']) && $configuracoes['notificacoes_backups'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="frequencia-notificacoes">Frequência de Notificações</label>
                        <select id="frequencia-notificacoes" class="form-control">
                            <option value="instantaneo" <?= isset($configuracoes['frequencia_notificacoes']) && $configuracoes['frequencia_notificacoes'] == "instantaneo" ? 'selected' : '' ?>>Instantâneo</option>
                            <option value="diario" <?= (!isset($configuracoes['frequencia_notificacoes'])) || $configuracoes['frequencia_notificacoes'] == "diario" ? 'selected' : '' ?>>Resumo Diário</option>
                            <option value="semanal" <?= isset($configuracoes['frequencia_notificacoes']) && $configuracoes['frequencia_notificacoes'] == "semanal" ? 'selected' : '' ?>>Resumo Semanal</option>
                        </select>
                    </div>
                    
                    <button class="btn-salvar" data-secao="notificacoes">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </section>
            </div>
        </div>
    </div>
</main>

<!-- Modal para Mudar Foto -->
<div id="modal-foto" class="modal">
    <div class="modal-conteudo">
        <div class="modal-cabecalho">
            <h2><i class="fas fa-camera"></i> Alterar Foto do Perfil</h2>
            <button onclick="fecharModalFoto()">&times;</button>
        </div>
        
        <div class="modal-formulario">
            <div class="form-grupo">
                <label for="nova-foto">Selecione uma nova imagem</label>
                <input type="file" id="nova-foto" accept="image/*">
            </div>
            
            <div class="form-grupo preview-container">
                <img id="preview-foto" src="#" alt="Pré-visualização">
            </div>
            
            <div class="modal-botoes">
                <button type="button" onclick="fecharModalFoto()">Cancelar</button>
                <button type="button" id="salvar-foto-btn"><i class="fas fa-save"></i> Salvar Foto</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineAdmin - Configurações</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="css/config_admin.css"> -->
</head>
<body>
    <header class="cabecalho">
        <div class="logo">
            <i class="fas fa-film"></i>
            <span>CineAdmin</span>
        </div>
        
        <nav class="nav-links">
            <a href="painel_admin.php"><i class="fas fa-home"></i> Início</a>
            <a href="series.php"><i class="fas fa-tv"></i> Séries</a>
            <a href="filmes.php"><i class="fas fa-film"></i> Filmes</a>
            <a href="destaques.php"><i class="fas fa-star"></i> Destaques</a>
        </nav>
        
        <div class="busca-usuario">
            <div class="usuario" onclick="toggleDropdown()">
                <img src="<?= isset($_SESSION['admin_foto']) ? $_SESSION['admin_foto'] : 'img/avatar-padrao.jpg' ?>" alt="Usuário">
                <div class="dropdown-usuario">
                    <a href="perfil_admin.php"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="#" onclick="abrirModalMudarFoto()"><i class="fas fa-camera"></i> Mudar Foto</a>
                    <a href="configuracoes.php"><i class="fas fa-cog"></i> Configurações</a>
                    <a href="usuarios_admin.php"><i class="fas fa-users-cog"></i> Usuários ADM</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="conteudo">
        <div class="config-container">
            <h1 class="config-titulo">
                <i class="fas fa-cog"></i>
                Configurações do Sistema
            </h1>
            
            <div class="config-grid">
                <!-- Menu Lateral -->
                <div class="config-menu">
                    <h3>Menu de Configurações</h3>
                    <div class="menu-links">
                        <a href="#aparencia" class="menu-link ativo">
                            <i class="fas fa-palette"></i> Aparência
                        </a>
                        <a href="#conta" class="menu-link">
                            <i class="fas fa-user-shield"></i> Conta
                        </a>
                        <a href="#notificacoes" class="menu-link">
                            <i class="fas fa-bell"></i> Notificações
                        </a>
                        <a href="#seguranca" class="menu-link">
                            <i class="fas fa-lock"></i> Segurança
                        </a>
                        <a href="#usuarios" class="menu-link">
                            <i class="fas fa-users"></i> Usuários
                        </a>
                        <a href="#sistema" class="menu-link">
                            <i class="fas fa-server"></i> Sistema
                        </a>
                        <a href="#backup" class="menu-link">
                            <i class="fas fa-database"></i> Backup
                        </a>
                    </div>
                </div>
                
                <!-- Conteúdo Principal -->
                <div class="config-conteudo">
                    <!-- Seção Aparência -->
                    <section id="aparencia" class="config-secao">
                        <h2><i class="fas fa-palette"></i> Aparência</h2>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Modo Escuro</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="modo-escuro" checked>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="tema-cor">Cor do Tema</label>
                            <select id="tema-cor" class="form-control">
                                <option value="#e50914" selected>Vermelho (Padrão)</option>
                                <option value="#007bff">Azul</option>
                                <option value="#28a745">Verde</option>
                                <option value="#6f42c1">Roxo</option>
                                <option value="#fd7e14">Laranja</option>
                                <option value="#17a2b8">Ciano</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="fonte">Fonte do Sistema</label>
                            <select id="fonte" class="form-control">
                                <option value="'Netflix Sans', 'Helvetica Neue', Arial, sans-serif" selected>Netflix Sans (Padrão)</option>
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="'Roboto', sans-serif">Roboto</option>
                                <option value="'Open Sans', sans-serif">Open Sans</option>
                                <option value="'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">Segoe UI</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="tamanho-fonte">Tamanho da Fonte</label>
                            <select id="tamanho-fonte" class="form-control">
                                <option value="14px">Pequeno</option>
                                <option value="16px" selected>Médio (Padrão)</option>
                                <option value="18px">Grande</option>
                            </select>
                        </div>
                        
                        <button class="btn-salvar">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Conta (oculta por padrão) -->
                    <section id="conta" class="config-secao" style="display: none;">
                        <h2><i class="fas fa-user-shield"></i> Configurações da Conta</h2>
                        
                        <div class="form-grupo">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" value="Administrador" placeholder="Seu nome completo">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" value="admin@cineadmin.com" placeholder="Seu e-mail">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="usuario">Nome de Usuário</label>
                            <input type="text" id="usuario" value="admin" placeholder="Nome de usuário">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="foto-perfil">Foto de Perfil</label>
                            <input type="file" id="foto-perfil" accept="image/*">
                        </div>
                        
                        <button class="btn-salvar">
                            <i class="fas fa-save"></i> Atualizar Conta
                        </button>
                    </section>
                    
                    <!-- Seção Notificações (oculta por padrão) -->
                    <section id="notificacoes" class="config-secao" style="display: none;">
                        <h2><i class="fas fa-bell"></i> Notificações</h2>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Ativar Notificações</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notificacoes-geral" checked>
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="form-grupo">
                            <label>Tipos de Notificação</label>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Novos Usuários</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-usuarios" checked>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                            </div>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Atualizações do Sistema</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-atualizacoes" checked>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                            </div>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Backups Automáticos</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-backups" checked>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="frequencia-notificacoes">Frequência de Notificações</label>
                            <select id="frequencia-notificacoes" class="form-control">
                                <option value="instantaneo">Instantâneo</option>
                                <option value="diario" selected>Resumo Diário</option>
                                <option value="semanal">Resumo Semanal</option>
                            </select>
                        </div>
                        
                        <button class="btn-salvar">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer_admin.php'; ?>
    
    <!-- Modal para Mudar Foto -->
    <div id="modal-foto" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center;">
        <div class="modal-conteudo" style="background-color: var(--card-bg); padding: 30px; border-radius: 8px; width: 90%; max-width: 500px;">
            <div class="modal-cabecalho" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--primary);"><i class="fas fa-camera"></i> Alterar Foto do Perfil</h2>
                <button onclick="fecharModalFoto()" style="background: none; border: none; color: var(--gray); font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            
            <form class="modal-formulario">
                <div class="form-grupo">
                    <label for="nova-foto">Selecione uma nova imagem</label>
                    <input type="file" id="nova-foto" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 5px;">
                </div>
                
                <div class="form-grupo" style="text-align: center; margin: 20px 0;">
                    <img id="preview-foto" src="#" alt="Pré-visualização" style="display: none; max-width: 200px; max-height: 200px; border-radius: 50%; border: 3px solid var(--primary);">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <button type="button" onclick="fecharModalFoto()" style="padding: 10px 20px; background-color: transparent; border: 1px solid var(--border-color); border-radius: 5px; color: var(--text-color); cursor: pointer;">Cancelar</button>
                    <button type="button" style="padding: 10px 20px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer;"><i class="fas fa-save"></i> Salvar Foto</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Toggle do dropdown do usuário
        function toggleDropdown() {
            document.querySelector('.usuario').classList.toggle('active');
        }

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            const usuario = document.querySelector('.usuario');
            if (!usuario.contains(event.target)) {
                usuario.classList.remove('active');
            }
        });

        // Alternar entre seções de configurações
        document.querySelectorAll('.menu-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove a classe ativo de todos os links
                document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('ativo'));
                
                // Adiciona a classe ativo apenas ao link clicado
                this.classList.add('ativo');
                
                // Oculta todas as seções
                document.querySelectorAll('.config-secao').forEach(sec => {
                    sec.style.display = 'none';
                });
                
                // Mostra apenas a seção correspondente
                const target = this.getAttribute('href');
                document.querySelector(target).style.display = 'block';
            });
        });

        // Alternar modo dark/light
        const modoEscuroToggle = document.getElementById('modo-escuro');
        
        // Verificar preferência do usuário
        if (localStorage.getItem('modoEscuro') === 'false') {
            document.body.classList.add('light-mode');
            modoEscuroToggle.checked = false;
        }
        
        modoEscuroToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.remove('light-mode');
                localStorage.setItem('modoEscuro', 'true');
            } else {
                document.body.classList.add('light-mode');
                localStorage.setItem('modoEscuro', 'false');
            }
        });

        // Mudar cor do tema
        const temaCor = document.getElementById('tema-cor');
        temaCor.addEventListener('change', function() {
            document.documentElement.style.setProperty('--primary', this.value);
            document.documentElement.style.setProperty('--primary-dark', darkenColor(this.value, 20));
            localStorage.setItem('temaCor', this.value);
        });

        // Carregar cor salva
        if (localStorage.getItem('temaCor')) {
            const corSalva = localStorage.getItem('temaCor');
            temaCor.value = corSalva;
            document.documentElement.style.setProperty('--primary', corSalva);
            document.documentElement.style.setProperty('--primary-dark', darkenColor(corSalva, 20));
        }

        // Função para escurecer cor
        function darkenColor(color, percent) {
            const num = parseInt(color.replace("#", ""), 16);
            const amt = Math.round(2.55 * percent);
            const R = (num >> 16) - amt;
            const G = (num >> 8 & 0x00FF) - amt;
            const B = (num & 0x0000FF) - amt;
            
            return "#" + (
                0x1000000 +
                (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 +
                (G < 255 ? (G < 1 ? 0 : G) : 255) * 0x100 +
                (B < 255 ? (B < 1 ? 0 : B) : 255)
            ).toString(16).slice(1);
        }

        // Mudar fonte
        const fonteSelect = document.getElementById('fonte');
        fonteSelect.addEventListener('change', function() {
            document.body.style.fontFamily = this.value;
            localStorage.setItem('fonte', this.value);
        });

        // Carregar fonte salva
        if (localStorage.getItem('fonte')) {
            fonteSelect.value = localStorage.getItem('fonte');
            document.body.style.fontFamily = localStorage.getItem('fonte');
        }

        // Mudar tamanho da fonte
        const tamanhoFonte = document.getElementById('tamanho-fonte');
        tamanhoFonte.addEventListener('change', function() {
            document.body.style.fontSize = this.value;
            localStorage.setItem('tamanhoFonte', this.value);
        });

        // Carregar tamanho de fonte salvo
        if (localStorage.getItem('tamanhoFonte')) {
            tamanhoFonte.value = localStorage.getItem('tamanhoFonte');
            document.body.style.fontSize = localStorage.getItem('tamanhoFonte');
        }

        // Modal para mudar foto
        function abrirModalMudarFoto() {
            document.getElementById('modal-foto').style.display = 'flex';
            document.querySelector('.usuario').classList.remove('active');
        }

        function fecharModalFoto() {
            document.getElementById('modal-foto').style.display = 'none';
        }

        // Pré-visualização da foto
        document.getElementById('nova-foto').addEventListener('change', function(e) {
            const preview = document.getElementById('preview-foto');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });

        // Fechar modal ao clicar fora
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('modal-foto')) {
                fecharModalFoto();
            }
        });

                // Verifica se os elementos existem antes de adicionar event listeners
        if (document.getElementById('modo-escuro')) {
            document.getElementById('modo-escuro').addEventListener('change', function() {
                // código do modo escuro
            });
        }

        // Funções globais que podem ser chamadas de qualquer página
        function toggleDropdown() {
            const usuario = document.querySelector('.usuario');
            if (usuario) usuario.classList.toggle('active');
        }

        function abrirModalMudarFoto() {
            const modal = document.getElementById('modal-foto');
            if (modal) modal.style.display = 'flex';
            
            const usuario = document.querySelector('.usuario');
            if (usuario) usuario.classList.remove('active');
        }
    </script>
</body>
</html>