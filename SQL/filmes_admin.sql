-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/05/2025 às 15:27
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `filmes_admin`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `nivel_acesso` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT 'img/avatar-padrao.jpg',
  `ultimo_login` datetime DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administradores`
--

INSERT INTO `administradores` (`id`, `nome`, `usuario`, `email`, `senha`, `data_criacao`, `nivel_acesso`, `foto_perfil`, `ultimo_login`, `tipo`) VALUES
(1, 'Suélen', 'teste', 'teste@teste.com', '$2y$10$0T5GIctDQCWT18gKSvEEfeleFYyv59SCcmPugoPwJNElileRY5dTS', '2025-05-10 22:41:30', NULL, 'img/avatar-padrao.jpg', NULL, 'admin'),
(2, 'Admin 1', 'admin1', 'admin1@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(3, 'Admin 2', 'admin2', 'admin2@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(4, 'Admin 3', 'admin3', 'admin3@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(5, 'Admin 4', 'admin4', 'admin4@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(6, 'Admin 5', 'admin5', 'admin5@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(7, 'Admin 6', 'admin6', 'admin6@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(8, 'Admin 7', 'admin7', 'admin7@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(9, 'Admin 8', 'admin8', 'admin8@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(10, 'Admin 9', 'admin9', 'admin9@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(11, 'Admin 10', 'admin10', 'admin10@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 00:37:47', 'super', 'img/avatar-padrao.jpg', NULL, 'admin'),
(13, '', 'adminm', 'admin@adminm.com', '$2y$10$z0ZMynRIWlJLqSlTTKz/ge6AmLPif0lIBzeyL0WGIzeryRL.9TKYO', '2025-05-14 00:57:52', NULL, 'img/avatar-padrao.jpg', NULL, 'admin'),
(17, '', 'admin', 'admin@admin.comm', '$2y$10$rs/bYWG7v/vpL9MvzCDC7.z3ZfBWI9RJ6jAd6Xj6bRxKuxeVhcuJ6', '2025-05-14 01:08:15', NULL, 'img/avatar-padrao.jpg', NULL, 'admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `catalogo`
--

CREATE TABLE `catalogo` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` enum('filme','serie') DEFAULT 'filme',
  `capa` varchar(500) DEFAULT NULL,
  `ano` year(4) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `catalogo`
--

INSERT INTO `catalogo` (`id`, `titulo`, `tipo`, `capa`, `ano`, `categoria`, `descricao`, `destaque`) VALUES
(64, 'O Poderoso Chefão', 'filme', 'uploads/capas/682430f5726a7_o_poderoso_chefão.jpg', '1972', 'Drama', 'Descrição do filme O Poderoso Chefão', 1),
(65, 'Forrest Gump', 'filme', 'uploads/capas/forrest_gump.jpg', '1994', 'Drama', 'Descrição do filme Forrest Gump', 0),
(66, 'A Origem', 'filme', 'uploads/capas/a_origem.jpg', '2010', 'Drama', 'Descrição do filme A Origem', 0),
(67, 'Interestelar', 'filme', 'uploads/capas/interestelar.jpg', '2014', 'Drama', 'Descrição do filme Interestelar', 1),
(68, 'Coringa', 'filme', 'uploads/capas/coringa.jpg', '2019', 'Drama', 'Descrição do filme Coringa', 0),
(69, 'Matrix', 'filme', 'uploads/capas/matrix.jpg', '1999', 'Drama', 'Descrição do filme Matrix', 0),
(70, 'Vingadores: Ultimato', 'filme', 'uploads/capas/vingadores_ultimato.jpg', '2019', 'Drama', 'Descrição do filme Vingadores: Ultimato', 1),
(71, 'Pantera Negra', 'filme', 'uploads/capas/pantera_negra.jpg', '2018', 'Drama', 'Descrição do filme Pantera Negra', 0),
(72, 'Gladiador', 'filme', 'uploads/capas/gladiador.jpg', '2000', 'Drama', 'Descrição do filme Gladiador', 0),
(73, 'Titanic', 'filme', 'uploads/capas/titanic.jpg', '1997', 'Drama', 'Descrição do filme Titanic', 1),
(74, 'Homem-Aranha: Sem Volta para Casa', 'filme', 'uploads/capas/homemaranha_sem_volta_para_casa.jpg', '2021', 'Drama', 'Descrição do filme Homem-Aranha: Sem Volta para Casa', 0),
(75, 'Duna', 'filme', 'uploads/capas/duna.jpg', '2021', 'Drama', 'Descrição do filme Duna', 0),
(76, 'Avatar', 'filme', 'uploads/capas/avatar.jpg', '2009', 'Drama', 'Descrição do filme Avatar', 1),
(77, 'Os Infiltrados', 'filme', 'uploads/capas/os_infiltrados.jpg', '2006', 'Drama', 'Descrição do filme Os Infiltrados', 0),
(78, 'Clube da Luta', 'filme', 'uploads/capas/clube_da_luta.jpg', '1999', 'Drama', 'Descrição do filme Clube da Luta', 0),
(79, 'Breaking Bad', 'serie', 'uploads/capas/breaking_bad.jpg', '2008', 'Drama', 'Professor vira fabricante de metanfetamina.', 1),
(80, 'Stranger Things', 'serie', 'uploads/capas/stranger_things.jpg', '2016', 'Ficção Científica', 'Grupo de crianças enfrenta fenômenos sobrenaturais.', 1),
(81, 'La Casa de Papel', 'serie', 'uploads/capas/la_casa_de_papel.jpg', '2017', 'Ação', 'Grupo realiza assalto à Casa da Moeda da Espanha.', 1),
(82, 'The Crown', 'serie', 'uploads/capas/the_crown.jpg', '2016', 'Histórico', 'A vida da Rainha Elizabeth II.', 0),
(83, 'Peaky Blinders', 'serie', 'uploads/capas/peaky_blinders.jpg', '2013', 'Drama', 'Gangue inglesa após a Primeira Guerra Mundial.', 0),
(84, 'The Mandalorian', 'serie', 'uploads/capas/68243369d9ae3_the_mandalorian.jpg', '2019', 'Aventura', 'Caçador de recompensas no universo Star Wars.', 1),
(85, 'The Office', 'serie', 'uploads/capas/the_office.jpg', '2005', 'Comédia', 'Vida cotidiana dos funcionários de um escritório.', 0),
(86, 'Dark', 'serie', 'uploads/capas/dark.jpg', '2017', 'Mistério', 'Viagens no tempo revelam segredos sombrios.', 1),
(87, 'Lupin', 'serie', 'uploads/capas/lupin.jpg', '2021', 'Suspense', 'Ladrão inspirado em Arsène Lupin busca vingança.', 0),
(88, 'Round 6', 'serie', 'uploads/capas/68243375386ae_round_6.jpg', '2021', 'Drama', 'Jogos mortais por dinheiro na Coreia.', 1),
(89, 'Black Mirror', 'serie', 'uploads/capas/black_mirror.jpg', '2011', 'Ficção Científica', 'Episódios independentes sobre tecnologia e sociedade.', 0),
(90, 'Vikings', 'serie', 'uploads/capas/vikings.jpg', '2013', 'Histórico', 'A saga do lendário guerreiro Ragnar Lothbrok.', 0),
(91, 'Wandinha', 'serie', 'uploads/capas/wandinha.jpg', '2022', 'Comédia', 'Filha dos Addams estuda em escola para jovens excluídos.', 1),
(92, 'The Boys', 'serie', 'uploads/capas/the_boys.jpg', '2019', 'Ação', 'Grupo combate super-heróis corruptos.', 1),
(93, 'How I Met Your Mother', 'serie', 'uploads/capas/6824339362005_how_i_met_your_mother.jpg', '2005', 'Comédia', 'Pai conta aos filhos como conheceu a mãe deles.', 0),
(94, 'O Poderoso Chefão', 'filme', 'uploads/capas/o_poderoso_chefão.jpg', '1972', 'Crime', 'A saga da família Corleone, liderada por Don Vito (Marlon Brando), mostra a brutalidade do mundo do crime em Nova York. Quando seu filho Michael (Al Pacino) assume o comando, a violência atinge novos patamares. Vencedor de 3 Oscars, incluindo Melhor Filme.', 1),
(95, 'Forrest Gump', 'filme', 'uploads/capas/forrest_gump.jpg', '1994', 'Drama', 'Forrest Gump (Tom Hanks), um homem simples com QI abaixo da média, vive momentos históricos dos EUA entre os anos 50 e 80. Da Guerra do Vietnã à criação da Apple, sua jornada emocionante mostra que a bondade pode vencer qualquer obstáculo. Oscar de Melhor Filme em 1995.', 0),
(96, 'A Origem', 'filme', 'uploads/capas/a_origem.jpg', '2010', 'Ficção Científica', 'Dom Cobb (Leonardo DiCaprio) é um especialista em roubar segredos durante o sono. Quando recebe a missão impossível de implantar uma ideia na mente de alguém, ele enfrenta os perigos da mente inconsciente. Direção de Christopher Nolan.', 0),
(97, 'Interestelar', 'filme', 'uploads/capas/interestelar.jpg', '2014', 'Ficção Científica', 'Num futuro onde a Terra está morrendo, o astronauta Cooper (Matthew McConaughey) viaja através de um buraco de minhoca em busca de um novo lar para a humanidade. Uma jornada épica que mistura ciência complexa e drama emocional. Dirigido por Christopher Nolan.', 1),
(98, 'Coringa', 'filme', 'uploads/capas/coringa.jpg', '2019', 'Drama', 'Arthur Fleck (Joaquin Phoenix) é um comediante fracassado que mergulha na loucura e se transforma no icônico vilão do Batman. Uma crítica social perturbadora que rendeu o Oscar de Melhor Ator. CUIDADO: Cenas fortes de violência.', 0),
(99, 'Matrix', 'filme', 'uploads/capas/matrix.jpg', '1999', 'Ação', 'Neo (Keanu Reeves) descobre que a realidade é uma simulação criada por máquinas. Liderado por Morpheus, ele se une à rebelião humana contra as IA. Revolucionou os efeitos visuais e a filosofia no cinema de ação.', 0),
(100, 'Vingadores: Ultimato', 'filme', 'uploads/capas/vingadores_ultimato.jpg', '2019', 'Ação', 'Os heróis restantes se unem para desfazer o estalo de Thanos e trazer de volta os desaparecidos. O épico final da Saga do Infinito da Marvel, com batalhas espetaculares e emocionantes despedidas. Maior bilheteria da história até 2021.', 1),
(101, 'Pantera Negra', 'filme', 'uploads/capas/pantera_negra.jpg', '2018', 'Ação', 'T\'Challa (Chadwick Boseman) assume o trono de Wakanda e enfrenta Killmonger, que desafia seu direito ao trono. Um marco da representatividade negra no cinema de super-heróis. Vencedor de 3 Oscars, incluindo Melhor Figurino.', 0),
(102, 'Gladiador', 'filme', 'uploads/capas/gladiador.jpg', '2000', 'Ação', 'Traído e escravizado, o general Máximo (Russell Crowe) se torna gladiador para se vingar do imperador Cômodo. Espetáculos de arena impressionantes e um dos melhores discursos do cinema (\"Eu terei minha vingança!\"). Oscar de Melhor Filme.', 0),
(103, 'Titanic', 'filme', 'uploads/capas/titanic.jpg', '1997', 'Romance', 'A bordo do navio \"inafundável\", a aristocrata Rose (Kate Winslet) e o artista pobre Jack (Leonardo DiCaprio) vivem um amor proibido durante o trágico naufrágio. Maior bilheteria por 12 anos e vencedor de 11 Oscars.', 1),
(104, 'Homem-Aranha: Sem Volta para Casa', 'filme', 'uploads/capas/homemaranha_sem_volta_para_casa.jpg', '2021', 'Ação', 'Peter Parker (Tom Holland) pede ao Dr. Estranho para apagar a memória de sua identidade, mas o feitiço dá errado e traz vilões de outros universos. A emocionante conclusão da trilogia do Homem-Aranha da Marvel.', 0),
(105, 'Duna', 'filme', 'uploads/capas/duna.jpg', '2021', 'Ficção Científica', 'Paul Atreides (Timothée Chalamet) lidera sua família na perigosa disputa pelo controle do planeta Arrakis, fonte da especiaria mais valiosa do universo. Adaptação épica do clássico de Frank Herbert. Vencedor de 6 Oscars técnicos.', 0),
(106, 'Avatar', 'filme', 'uploads/capas/avatar.jpg', '2009', 'Ficção Científica', 'No planeta Pandora, o paraplégico Jake Sully (Sam Worthington) se conecta a um corpo alienígena e se apaixona por Neytiri. Revolucionário em efeitos 3D, foi o filme mais lucrativo da história por uma década.', 1),
(107, 'Os Infiltrados', 'filme', 'uploads/capas/os_infiltrados.jpg', '2006', 'Suspense', 'Um policial (Leonardo DiCaprio) se infiltra na máfia de Boston, enquanto um criminoso (Matt Damon) entra para a polícia. Jogo de gato e rato com reviravoltas surpreendentes. Oscar de Melhor Filme para Martin Scorsese.', 0),
(108, 'Clube da Luta', 'filme', 'uploads/capas/clube_da_luta.jpg', '1999', 'Drama', 'Um homem insone (Edward Norton) e o carismático Tyler Durden (Brad Pitt) criam um clube clandestino onde homens lutam para fugir do consumismo. Cult absoluto com críticas ácidas à sociedade moderna.', 0),
(109, 'Breaking Bad', 'serie', 'uploads/capas/breaking_bad.jpg', '2008', 'Drama', 'Walter White (Bryan Cranston), um professor de química com câncer terminal, vira produtor de metanfetamina para garantir o futuro da família. Sua transformação em Heisenberg é uma das melhores jornadas de personagem da TV. 16 Emmys e considerada a melhor série de todos os tempos.', 1),
(110, 'Stranger Things', 'serie', 'uploads/capas/stranger_things.jpg', '2016', 'Ficção Científica', 'Nos anos 1980, em Hawkins, um grupo de crianças enfrenta criaturas do \"Mundo Invertido\" após o desaparecimento de Will. Com Eleven (Millie Bobby Brown) e referências nostálgicas aos filmes de Spielberg, a série conquistou fãs mundialmente.', 0),
(111, 'La Casa de Papel', 'serie', 'uploads/capas/la_casa_de_papel.jpg', '2017', 'Crime', 'O Professor (Álvaro Morte) planeja o maior assalto da história: roubar a Casa da Moeda da Espanha. Com codinomes de cidades e máscaras de Dalí, os ladrões enfrentam reféns e polícia em um jogo psicológico eletrizante.', 1),
(112, 'The Crown', 'serie', 'uploads/capas/the_crown.jpg', '2016', 'Drama', 'A vida da rainha Elizabeth II, desde seu casamento em 1947 até os eventos políticos e pessoais que moldaram o século XX. Produção luxuosa da Netflix com atenção obsessiva a detalhes históricos. 21 Emmys.', 0),
(113, 'Peaky Blinders', 'serie', 'uploads/capas/peaky_blinders.jpg', '2013', 'Drama', 'Na Birmingham pós-Primeira Guerra, a gangue Shelby - liderada por Tommy (Cillian Murphy) - usa navalhas nos chapéus para dominar o submundo. Fotografia estilizada e trilha sonora moderna em uma série de época brutal.', 1),
(114, 'The Mandalorian', 'serie', 'uploads/capas/the_mandalorian.jpg', '2019', 'Ficção Científica', 'Um caçador de recompensas (Pedro Pascal) protege Grogu (\"Baby Yoda\"), uma criança da mesma espécie do Mestre Yoda. A série que revitalizou Star Wars com western espacial e efeitos práticos. Vencedora de 7 Emmys.', 0),
(115, 'The Office', 'serie', 'uploads/capas/the_office.jpg', '2005', 'Comédia', 'Falso documentário sobre os funcionários disfuncionais da Dunder Mifflin Paper Company. Com Michael Scott (Steve Carell) como o chefe mais constrangedor da TV. Influenciou toda uma geração de comédias.', 1),
(116, 'Dark', 'serie', 'uploads/capas/dark.jpg', '2017', 'Ficção Científica', 'O desaparecimento de crianças em Winden revela segredos intergeracionais e viagens no tempo. A série alemã mais complexa da Netflix exige atenção total para desvendar seus mistérios temporais.', 0),
(117, 'Lupin', 'serie', 'uploads/capas/lupin.jpg', '2021', 'Crime', 'Assane Diop (Omar Sy) usa as histórias do ladrão Arsène Lupin para vingar seu pai, injustamente acusado de roubo. Misturando elegância parisiense e reviravoltas inteligentes, é a primeira série francesa de sucesso global da Netflix.', 1),
(118, 'Round 6', 'serie', 'uploads/capas/round_6.jpg', '2021', 'Suspense', 'Endividados, 456 pessoas competem em jogos infantis mortais por um prêmio bilionário. A série coreana que quebrou recordes na Netflix mistura crítica social e violência extrema em uma alegoria do capitalismo.', 0),
(119, 'Black Mirror', 'serie', 'uploads/capas/black_mirror.jpg', '2011', 'Ficção Científica', 'Episódios independentes exploram o lado sombrio da tecnologia e seus impactos na sociedade. De reality shows distópicos a cookies digitais conscientes, cada história é um alerta sobre nosso futuro próximo.', 1),
(120, 'Vikings', 'serie', 'uploads/capas/vikings.jpg', '2013', 'Ação', 'A saga de Ragnar Lothbrok (Travis Fimmel), do humilde fazendeiro a lendário rei viking, e seus filhos que exploram novos territórios. Batalhas sangrentas, mitologia nórdica e uma trilha sonora épica.', 0),
(121, 'Wandinha', 'serie', 'uploads/capas/wandinha.jpg', '2022', 'Terror', 'Wandinha Addams (Jenna Ortega) investiga assassinatos na Academia Nevermore enquanto controla seus poderes sobrenaturais. Mistura de terror, comédia e coming-of-age que revitalizou a Família Addams.', 1),
(122, 'The Boys', 'serie', 'uploads/capas/the_boys.jpg', '2019', 'Ação', 'Num mundo onde super-heróis corruptos são controlados por uma megacorporação, um grupo de vigilantes os enfrenta com métodos brutais. Sátira violenta ao universo de super-heróis com Antony Starr como o psicopata Homelander.', 0),
(123, 'How I Met Your Mother', 'serie', 'uploads/capas/how_i_met_your_mother.jpg', '2005', 'Comédia', 'Ted Mosby (Josh Radnor) conta aos filhos como conheceu a mãe deles, revisitando suas aventuras amorosas e a turma do MacLaren\'s Pub em Nova York. Neil Patrick Harris rouba a cena como o lendário Barney Stinson.', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_sistema`
--

CREATE TABLE `configuracoes_sistema` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `configuracoes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes_sistema`
--

INSERT INTO `configuracoes_sistema` (`id`, `admin_id`, `configuracoes`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"aparencia\":{\"modo_escuro\":true,\"tema_cor\":\"#e50914\",\"fonte\":\"\'Sans\', \'Helvetica Neue\', Arial, sans-serif\",\"tamanho_fonte\":\"16px\"}}', '2025-05-11 05:06:13', '2025-05-12 04:49:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_usuarios`
--

CREATE TABLE `lista_usuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `conteudo_id` int(11) NOT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `assistido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lista_usuarios`
--

INSERT INTO `lista_usuarios` (`id`, `usuario_id`, `conteudo_id`, `data_adicao`, `assistido`) VALUES
(31, 1, 70, '2025-05-14 06:06:33', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tickets_historico`
--

CREATE TABLE `tickets_historico` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `data_acao` datetime NOT NULL,
  `acao` varchar(100) NOT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tickets_historico`
--

INSERT INTO `tickets_historico` (`id`, `ticket_id`, `usuario_id`, `data_acao`, `acao`, `comentario`) VALUES
(1, 1, 1, '0000-00-00 00:00:00', 'Status alterado para: Não Resolvido', 'aaa'),
(2, 2, 1, '0000-00-00 00:00:00', 'Status alterado para: Fechado', ''),
(3, 3, 1, '0000-00-00 00:00:00', 'Status alterado para: Não Resolvido', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tickets_suporte`
--

CREATE TABLE `tickets_suporte` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `assunto` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `data_abertura` datetime NOT NULL,
  `status` enum('aberto','em_andamento','resolvido') DEFAULT 'aberto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tickets_suporte`
--

INSERT INTO `tickets_suporte` (`id`, `usuario_id`, `assunto`, `mensagem`, `data_abertura`, `status`) VALUES
(1, 1, 'tecnico', 'aaaa', '2025-05-13 21:04:21', ''),
(2, 1, 'conta', 'aaaa', '2025-05-13 22:47:43', ''),
(3, 1, 'pagamento', 'help', '2025-05-13 22:51:12', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `senha`, `criado_em`, `nome`) VALUES
(1, 'suelen', 'suelen@suelen.com', '$2y$10$9e8isl1ozCDHYdSyb2Glju4AdGkG2rBZjVQU6WCrgjb.A//EOOqsC', '2025-05-13 03:29:55', 'Suélen'),
(5, 'user1', 'user1@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 1'),
(6, 'user2', 'user2@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 2'),
(7, 'user3', 'user3@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 3'),
(8, 'user4', 'user4@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 4'),
(9, 'user5', 'user5@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 5'),
(10, 'user6', 'user6@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 6'),
(11, 'user7', 'user7@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 7'),
(12, 'user8', 'user8@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 8'),
(13, 'user9', 'user9@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 9'),
(14, 'user10', 'user10@exemplo.com', 'e7d80ffeefa212b7c5c55700e4f7193e', '2025-05-14 03:39:42', 'Usuário 10'),
(19, 'aaaaaa', 'aaaa@aaaaa.com', '$2y$10$J0vFKTVgo1MbyGZkQQY2zeoGPFg9srCNPgE6de.NP3ac0QCh2iGjq', '2025-05-14 04:05:06', ''),
(20, 'aaaa', 'aaa@aaaa.com', '$2y$10$v7.v2YjuqjOiaPKPqWdPQe22tI.6xyA4YYrDk1EUugjg3nsLoNdXG', '2025-05-14 04:05:35', ''),
(21, 'admin', 'admin@admin.com', '$2y$10$CZIvMaEkeD2pdaCub2fKbOgNj.SbOSsQ8yYCRTQDV8Xu6e6rAov0u', '2025-05-14 04:06:01', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `catalogo`
--
ALTER TABLE `catalogo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `configuracoes_sistema`
--
ALTER TABLE `configuracoes_sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Índices de tabela `lista_usuarios`
--
ALTER TABLE `lista_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`conteudo_id`),
  ADD KEY `conteudo_id` (`conteudo_id`);

--
-- Índices de tabela `tickets_historico`
--
ALTER TABLE `tickets_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Índices de tabela `tickets_suporte`
--
ALTER TABLE `tickets_suporte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `catalogo`
--
ALTER TABLE `catalogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT de tabela `configuracoes_sistema`
--
ALTER TABLE `configuracoes_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `lista_usuarios`
--
ALTER TABLE `lista_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `tickets_historico`
--
ALTER TABLE `tickets_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tickets_suporte`
--
ALTER TABLE `tickets_suporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `configuracoes_sistema`
--
ALTER TABLE `configuracoes_sistema`
  ADD CONSTRAINT `configuracoes_sistema_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administradores` (`id`);

--
-- Restrições para tabelas `lista_usuarios`
--
ALTER TABLE `lista_usuarios`
  ADD CONSTRAINT `lista_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lista_usuarios_ibfk_2` FOREIGN KEY (`conteudo_id`) REFERENCES `catalogo` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tickets_suporte`
--
ALTER TABLE `tickets_suporte`
  ADD CONSTRAINT `tickets_suporte_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
