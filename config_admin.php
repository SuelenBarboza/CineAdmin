<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit();
}

$paginaTitulo = "Configurações";

require __DIR__ . '/header_admin.php';

// Carregar configurações do banco de dados
$configuracoes = [];

$mysqli = conexao();
if ($mysqli->connect_error) {
    error_log("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
} else {
    try {
        $admin_id = $_SESSION['admin_id'];
        $query = "SELECT * FROM configuracoes_sistema WHERE admin_id = ?";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $admin_id);
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

// Valores padrão
$configuracoes = array_merge([
    'modo_escuro' => true, 
    'tema_cor' => '#e50914', 
    'fonte' => "'Sans', 'Helvetica Neue', Arial, sans-serif",
    'tamanho_fonte' => '16px',
    'notificacoes_geral' => true,
    'notificacoes_usuarios' => true,
    'notificacoes_atualizacoes' => true,
    'notificacoes_backups' => false,
    'frequencia_notificacoes' => 'diario',
    'autenticacao_2fatores' => false,
    'bloqueio_tentativas' => 5,
    'tempo_bloqueio' => 30,
    'registro_login' => true,
    'politica_senha' => 'media',
    'limite_usuarios' => 100,
    'permissoes_padrao' => 'moderador',
    'auto_aprovacao' => false,
    'manutencao' => false,
    'timezone' => 'America/Sao_Paulo',
    'idioma' => 'pt_BR',
    'registro_erros' => true,
    'frequencia_backup' => 'semanal',
    'manter_backups' => 4,
    'backup_cloud' => false
], $configuracoes);

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($paginaTitulo) ?> - CineAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/config_admin.css">
    <style>
        .config-secao {
            display: none;
        }
        .config-secao.ativo {
            display: block;
        }
    </style>
</head>
<body>
    <main class="conteudo">
        <div class="config-container">
            <h1 class="config-titulo">
                <i class="fas fa-cog"></i>
                Configurações do Sistema
            </h1>
            
            <div class="config-grid">
                <!-- Menu Lateral -->
                <div class="menu-links">
                    <a href="#" class="menu-link ativo" data-secao="aparencia">
                        <i class="fas fa-palette"></i> Aparência
                    </a>
                    <a href="#conta" class="menu-link" data-secao="conta">
                        <i class="fas fa-user-shield"></i> Conta
                    </a>
                    <a href="#notificacoes" class="menu-link" data-secao="notificacoes">
                        <i class="fas fa-bell"></i> Notificações
                    </a>
                    <a href="#seguranca" class="menu-link" data-secao="seguranca">
                        <i class="fas fa-lock"></i> Segurança
                    </a>
                    <a href="#usuarios" class="menu-link" data-secao="usuarios">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                    <a href="#sistema" class="menu-link" data-secao="sistema">
                        <i class="fas fa-server"></i> Sistema
                    </a>
                    <a href="#backup" class="menu-link" data-secao="backup">
                        <i class="fas fa-database"></i> Backup
                    </a>
                    
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
                                <option value="#fd7e14" <?= $configuracoes['tema_cor'] == '#fd7e14' ? 'selected' : '' ?>>Laranja</option>
                                <option value="#17a2b8" <?= $configuracoes['tema_cor'] == '#17a2b8' ? 'selected' : '' ?>>Ciano</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="fonte">Fonte do Sistema</label>
                            <select id="fonte" class="form-control">
                                <option value="'Sans', 'Helvetica Neue', Arial, sans-serif" <?= $configuracoes['fonte'] === "'Sans', 'Helvetica Neue', Arial, sans-serif" ? 'selected' : '' ?>> Sans</option>
                                <option value="Arial, sans-serif" <?= $configuracoes['fonte'] === "Arial, sans-serif" ? 'selected' : '' ?>>Arial</option>
                                <option value="'Roboto', sans-serif" <?= $configuracoes['fonte'] === "'Roboto', sans-serif" ? 'selected' : '' ?>>Roboto</option>
                                <option value="'Open Sans', sans-serif" <?= $configuracoes['fonte'] === "'Open Sans', sans-serif" ? 'selected' : '' ?>>Open Sans</option>
                                <option value="'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" <?= $configuracoes['fonte'] === "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" ? 'selected' : '' ?>>Segoe UI</option>
                            </select>

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
                                    <input type="checkbox" id="notificacoes-geral" <?= $configuracoes['notificacoes_geral'] ? 'checked' : '' ?>>
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
                                        <input type="checkbox" id="notificacoes-usuarios" <?= $configuracoes['notificacoes_usuarios'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                            </div>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Atualizações do Sistema</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-atualizacoes" <?= $configuracoes['notificacoes_atualizacoes'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </label>
                            </div>
                            
                            <div class="form-grupo">
                                <label class="toggle-label">
                                    <span class="toggle-text">Backups Automáticos</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="notificacoes-backups" <?= $configuracoes['notificacoes_backups'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </label>
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
                        
                        <button type="button" class="btn-salvar" data-secao="aparencia">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Segurança -->
                    <section id="seguranca" class="config-secao" style="display: none;">
                        <h2><i class="fas fa-lock"></i> Segurança</h2>
                        
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
                        
                        <div class="form-grupo">
                            <label for="bloqueio-tentativas">Tentativas de Login Antes do Bloqueio</label>
                            <input type="number" id="bloqueio-tentativas" min="1" max="10" value="<?= $configuracoes['bloqueio_tentativas'] ?>">
                        </div>
                        
                        <div class="form-grupo">
                            <label for="tempo-bloqueio">Tempo de Bloqueio (minutos)</label>
                            <input type="number" id="tempo-bloqueio" min="1" max="1440" value="<?= $configuracoes['tempo_bloqueio'] ?>">
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Registro de Logins</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="registro-login" <?= $configuracoes['registro_login'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Registra todos os acessos ao painel administrativo</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="politica-senha">Política de Senhas</label>
                            <select id="politica-senha" class="form-control">
                                <option value="fraca" <?= $configuracoes['politica_senha'] == "fraca" ? 'selected' : '' ?>>Fraca (mínimo 6 caracteres)</option>
                                <option value="media" <?= $configuracoes['politica_senha'] == "media" ? 'selected' : '' ?>>Média (mínimo 8 caracteres com letras e números)</option>
                                <option value="forte" <?= $configuracoes['politica_senha'] == "forte" ? 'selected' : '' ?>>Forte (mínimo 10 caracteres com letras, números e símbolos)</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <button id="btn-alterar-senha" class="btn-secundario">
                                <i class="fas fa-key"></i> Alterar Senha
                            </button>
                        </div>
                        
                        <button class="btn-salvar" data-secao="seguranca">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Usuários -->
                    <section id="usuarios" class="config-secao" style="display: none;">
                        <h2><i class="fas fa-users"></i> Configurações de Usuários</h2>
                        
                        <div class="form-grupo">
                            <label for="limite-usuarios">Limite Máximo de Usuários</label>
                            <input type="number" id="limite-usuarios" min="1" max="10000" value="<?= $configuracoes['limite_usuarios'] ?>">
                            <p class="form-texto-ajuda">Defina 0 para ilimitado</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="permissoes-padrao">Permissões Padrão para Novos Usuários</label>
                            <select id="permissoes-padrao" class="form-control">
                                <option value="leitura" <?= $configuracoes['permissoes_padrao'] == "leitura" ? 'selected' : '' ?>>Somente Leitura</option>
                                <option value="moderador" <?= $configuracoes['permissoes_padrao'] == "moderador" ? 'selected' : '' ?>>Moderador</option>
                                <option value="editor" <?= $configuracoes['permissoes_padrao'] == "editor" ? 'selected' : '' ?>>Editor</option>
                                <option value="administrador" <?= $configuracoes['permissoes_padrao'] == "administrador" ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Aprovação Automática de Novos Usuários</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="auto-aprovacao" <?= $configuracoes['auto_aprovacao'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Se desativado, novos usuários precisarão ser aprovados manualmente</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label>Permissões Avançadas</label>
                            <div class="permissoes-lista">
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Criar Conteúdo
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Editar Conteúdo
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" checked> Excluir Conteúdo
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Gerenciar Usuários
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox"> Alterar Configurações
                                </label>
                            </div>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="aparencia">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Sistema -->
                    <section id="sistema" class="config-secao" style="display: none;">
                        <h2><i class="fas fa-server"></i> Configurações do Sistema</h2>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Modo de Manutenção</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="manutencao" <?= $configuracoes['manutencao'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Quando ativado, apenas administradores podem acessar o sistema</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="timezone">Fuso Horário</label>
                            <select id="timezone" class="form-control">
                                <option value="America/Sao_Paulo" <?= $configuracoes['timezone'] == "America/Sao_Paulo" ? 'selected' : '' ?>>Brasília (GMT-3)</option>
                                <option value="America/New_York" <?= $configuracoes['timezone'] == "America/New_York" ? 'selected' : '' ?>>Nova York (GMT-4/-5)</option>
                                <option value="Europe/London" <?= $configuracoes['timezone'] == "Europe/London" ? 'selected' : '' ?>>Londres (GMT+0/+1)</option>
                                <option value="Asia/Tokyo" <?= $configuracoes['timezone'] == "Asia/Tokyo" ? 'selected' : '' ?>>Tóquio (GMT+9)</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="idioma">Idioma do Sistema</label>
                            <select id="idioma" class="form-control">
                                <option value="pt_BR" <?= $configuracoes['idioma'] == "pt_BR" ? 'selected' : '' ?>>Português (Brasil)</option>
                                <option value="en_US" <?= $configuracoes['idioma'] == "en_US" ? 'selected' : '' ?>>Inglês (EUA)</option>
                                <option value="es_ES" <?= $configuracoes['idioma'] == "es_ES" ? 'selected' : '' ?>>Espanhol</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Registro de Erros</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="registro-erros" <?= $configuracoes['registro_erros'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Registra erros do sistema em arquivo de log</p>
                        </div>
                        
                        <div class="form-grupo">
                            <button id="btn-limpar-cache" class="btn-secundario">
                                <i class="fas fa-broom"></i> Limpar Cache do Sistema
                            </button>
                        </div>
                        
                        <div class="form-grupo">
                            <button id="btn-verificar-atualizacoes" class="btn-secundario">
                                <i class="fas fa-sync-alt"></i> Verificar Atualizações
                            </button>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="aparencia">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </section>
                    
                    <!-- Seção Backup -->
                    <section id="backup" class="config-secao" style="display: none;">
                        <h2><i class="fas fa-database"></i> Backup do Sistema</h2>
                        
                        <div class="form-grupo">
                            <label for="frequencia-backup">Frequência de Backup Automático</label>
                            <select id="frequencia-backup" class="form-control">
                                <option value="diario" <?= $configuracoes['frequencia_backup'] == "diario" ? 'selected' : '' ?>>Diário</option>
                                <option value="semanal" <?= $configuracoes['frequencia_backup'] == "semanal" ? 'selected' : '' ?>>Semanal</option>
                                <option value="mensal" <?= $configuracoes['frequencia_backup'] == "mensal" ? 'selected' : '' ?>>Mensal</option>
                                <option value="desativado" <?= $configuracoes['frequencia_backup'] == "desativado" ? 'selected' : '' ?>>Desativado</option>
                            </select>
                        </div>
                        
                        <div class="form-grupo">
                            <label for="manter-backups">Manter Últimos Backups</label>
                            <select id="manter-backups" class="form-control">
                                <option value="1" <?= $configuracoes['manter_backups'] == 1 ? 'selected' : '' ?>>1 Backup</option>
                                <option value="2" <?= $configuracoes['manter_backups'] == 2 ? 'selected' : '' ?>>2 Backups</option>
                                <option value="4" <?= $configuracoes['manter_backups'] == 4 ? 'selected' : '' ?>>4 Backups</option>
                                <option value="10" <?= $configuracoes['manter_backups'] == 10 ? 'selected' : '' ?>>10 Backups</option>
                            </select>
                            <p class="form-texto-ajuda">Os backups mais antigos serão excluídos automaticamente</p>
                        </div>
                        
                        <div class="form-grupo">
                            <label class="toggle-label">
                                <span class="toggle-text">Armazenar Backup na Nuvem</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="backup-cloud" <?= $configuracoes['backup_cloud'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <p class="form-texto-ajuda">Requer configuração adicional de serviços de nuvem</p>
                        </div>
                        
                        <div class="form-grupo">
                            <button id="btn-backup-agora" class="btn-primario">
                                <i class="fas fa-download"></i> Criar Backup Agora
                            </button>
                            <p class="form-texto-ajuda">Cria um backup manual do banco de dados e arquivos do sistema</p>
                        </div>
                        
                        <div class="form-grupo">
                            <h3>Backups Disponíveis</h3>
                            <div class="backups-lista">
                                <div class="backup-item">
                                    <i class="fas fa-file-archive"></i>
                                    <div class="backup-info">
                                        <span class="backup-nome">backup_20230515.zip</span>
                                        <span class="backup-data">15/05/2023 14:30 - 45.7 MB</span>
                                    </div>
                                    <button class="btn-backup-acao"><i class="fas fa-download"></i></button>
                                    <button class="btn-backup-acao"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="backup-item">
                                    <i class="fas fa-file-archive"></i>
                                    <div class="backup-info">
                                        <span class="backup-nome">backup_20230508.zip</span>
                                        <span class="backup-data">08/05/2023 14:30 - 44.2 MB</span>
                                    </div>
                                    <button class="btn-backup-acao"><i class="fas fa-download"></i></button>
                                    <button class="btn-backup-acao"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn-salvar" data-secao="aparencia">
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

    <!-- Modal para Alterar Senha -->
    <div id="modal-senha" class="modal">
        <div class="modal-conteudo">
            <div class="modal-cabecalho">
                <h2><i class="fas fa-key"></i> Alterar Senha</h2>
                <button onclick="fecharModalSenha()">&times;</button>
            </div>
            
            <div class="modal-formulario">
                <div class="form-grupo">
                    <label for="senha-atual">Senha Atual</label>
                    <input type="password" id="senha-atual" placeholder="Digite sua senha atual">
                </div>
                
                <div class="form-grupo">
                    <label for="nova-senha">Nova Senha</label>
                    <input type="password" id="nova-senha" placeholder="Digite a nova senha">
                </div>
                
                <div class="form-grupo">
                    <label for="confirmar-senha">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar-senha" placeholder="Digite novamente a nova senha">
                </div>
                
                <div class="modal-botoes">
                    <button type="button" onclick="fecharModalSenha()">Cancelar</button>
                    <button type="button" id="salvar-senha-btn"><i class="fas fa-save"></i> Alterar Senha</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/config_admin.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar a primeira seção por padrão
    document.getElementById('aparencia').style.display = 'block';

    // Função para alternar entre seções
    function alternarSecao(secaoId) {
        // Esconde todas as seções
        document.querySelectorAll('.config-secao').forEach(sec => {
            sec.style.display = 'none';
        });
        
        // Mostra a seção correspondente
        document.getElementById(secaoId).style.display = 'block';
        
        // Atualiza o menu ativo
        document.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('ativo');
            if (link.getAttribute('data-secao') === secaoId) {
                link.classList.add('ativo');
            }
        });
    }

    // Configurar clique nos links do menu
    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const secaoId = this.getAttribute('data-secao');
            alternarSecao(secaoId);
        });
    });

    // Configurar o salvamento - apenas para os botões de salvar
    document.querySelectorAll('.btn-salvar').forEach(botao => {
        botao.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const secao = this.getAttribute('data-secao');
            let dados = {};

            // Coletar dados específicos de cada seção
            if (secao === 'aparencia') {
                dados = {
                    modo_escuro: document.getElementById('modo-escuro').checked,
                    tema_cor: document.getElementById('tema-cor').value,
                    fonte: document.getElementById('fonte').value,
                    tamanho_fonte: document.getElementById('tamanho-fonte').value
                };
            } else if (secao === 'notificacoes') {
                dados = {
                    notificacoes_geral: document.getElementById('notificacoes-geral').checked,
                    notificacoes_usuarios: document.getElementById('notificacoes-usuarios').checked,
                    notificacoes_atualizacoes: document.getElementById('notificacoes-atualizacoes').checked,
                    notificacoes_backups: document.getElementById('notificacoes-backups').checked,
                    frequencia_notificacoes: document.getElementById('frequencia-notificacoes').value
                };
            }
            // Adicione outras seções conforme necessário

            try {
                const response = await fetch('salvar_config.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        secao: secao,
                        dados: dados
                    })
                });

                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Erro ao salvar configurações');
                }

                alert('Configurações salvas com sucesso!');
                console.log('Resposta do servidor:', result);
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao salvar: ' + error.message);
            }
        });
    });
});
</script>
    
</body>
</html>