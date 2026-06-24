-- Criação do banco de dados JK Game Match
CREATE DATABASE IF NOT EXISTS jk_game_match;
USE jk_game_match;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nickname VARCHAR(50) UNIQUE NOT NULL,
    foto_perfil VARCHAR(255),
    bio TEXT,
    rank VARCHAR(50),
    plataforma VARCHAR(50),
    nivel INT DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP NULL DEFAULT NULL
);

-- Tabela de jogos
CREATE TABLE IF NOT EXISTS jogos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL UNIQUE,
    genero VARCHAR(50),
    plataforma VARCHAR(100),
    imagem VARCHAR(255),
    descricao TEXT,
    data_lancamento DATE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para tokens de recuperação de senha
CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(128) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (`token`),
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de jogos favoritos do usuário
CREATE TABLE IF NOT EXISTS jogos_favoritos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    jogo_id INT NOT NULL,
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (jogo_id) REFERENCES jogos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (usuario_id, jogo_id)
);

-- Tabela de matches (encontros entre jogadores)
CREATE TABLE IF NOT EXISTS matches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario1_id INT NOT NULL,
    usuario2_id INT NOT NULL,
    jogo_id INT NOT NULL,
    status ENUM('pendente', 'aceito', 'recusado', 'finalizado') DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_resposta TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (usuario1_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario2_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (jogo_id) REFERENCES jogos(id) ON DELETE CASCADE
);

-- Tabela de mensagens
CREATE TABLE IF NOT EXISTS mensagens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    remetente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    conteudo TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Inserir jogos populares
INSERT INTO jogos (nome, genero, plataforma, descricao) VALUES
('Counter-Strike 2', 'FPS', 'PC', 'Jogo de tiro em primeira pessoa competitivo'),
('League of Legends', 'MOBA', 'PC', 'Jogo de arena de batalha multiplayer online'),
('Valorant', 'FPS', 'PC', 'Jogo tático de tiro em primeira pessoa'),
('Dota 2', 'MOBA', 'PC', 'Jogo de arena de batalha estratégico'),
('Minecraft', 'Sandbox', 'Multi-Plataforma', 'Jogo de construção e exploração'),
('Fortnite', 'Battle Royale', 'Multi-Plataforma', 'Jogo de sobrevivência e construção'),
('Call of Duty: Warzone', 'Battle Royale', 'Multi-Plataforma', 'Jogo de tiro battle royale'),
('Apex Legends', 'Battle Royale', 'Multi-Plataforma', 'Jogo de heróis em batalha royale'),
('Elden Ring', 'RPG', 'Multi-Plataforma', 'Jogo de RPG de ação dark fantasy'),
('Palworld', 'RPG', 'Multi-Plataforma', 'Jogo de captura e criação de criaturas'),
('The Crew Motorfest', 'Racing', 'Multi-Plataforma', 'Jogo de corrida online cooperativo'),
('Rainbow Six Siege', 'FPS', 'Multi-Plataforma', 'Jogo de tiro tático com destruição dinâmica'),
('Overwatch 2', 'FPS', 'Multi-Plataforma', 'Jogo de tiro por equipes com heróis'),
('Hearthstone', 'Card Game', 'Multi-Plataforma', 'Jogo de cartas colecionáveis'),
('World of Warcraft', 'MMORPG', 'PC', 'Jogo de RPG multijogador massivo');
