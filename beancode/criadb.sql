-- ============================================================================
-- SCRIPT COMPLETO DE CRIAÇÃO E POPULAÇÃO DO BANCO DE DADOS BEANCODE
-- ============================================================================
-- Este script cria o banco de dados, todas as tabelas e popula com dados iniciais
-- Execute com: mysql -u root < criadb.sql
-- ============================================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS `db_beancode` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_beancode`;

-- Desabilita verificação de chaves estrangeiras temporariamente
SET FOREIGN_KEY_CHECKS = 0;

-- Remove tabelas existentes na ordem correta
DROP TABLE IF EXISTS `progresso_licoes`;
DROP TABLE IF EXISTS `aluno_conquistas`;
DROP TABLE IF EXISTS `licoes`;
DROP TABLE IF EXISTS `modulos`;
DROP TABLE IF EXISTS `cursos`;
DROP TABLE IF EXISTS `notificacoes`;
DROP TABLE IF EXISTS `alunos`;
DROP TABLE IF EXISTS `caminhos`;
DROP TABLE IF EXISTS `conquistas`;
DROP TABLE IF EXISTS `responsaveis`;

-- ============================================================================
-- CRIAÇÃO DAS TABELAS
-- ============================================================================

-- Tabela de responsáveis
CREATE TABLE `responsaveis` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome_user` VARCHAR(30) DEFAULT NULL,
    `nome_completo` VARCHAR(50) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `data_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de alunos
CREATE TABLE `alunos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `responsavel_id` INT(11) NOT NULL,
    `nome_user` VARCHAR(30) NOT NULL,
    `nome_completo` VARCHAR(50) DEFAULT NULL,
    `trilha_ativa` VARCHAR(50) DEFAULT 'iniciante',
    `senha` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nome_user` (`nome_user`),
    KEY `responsavel_id` (`responsavel_id`),
    FOREIGN KEY (`responsavel_id`) REFERENCES `responsaveis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de caminhos (trilhas)
CREATE TABLE `caminhos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(50) NOT NULL,
    `descricao` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de conquistas
CREATE TABLE `conquistas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(50) NOT NULL,
    `descricao` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de cursos
CREATE TABLE `cursos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome_curso` VARCHAR(50) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `duracao` INT(11) NOT NULL,
    `caminho_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `caminho_id` (`caminho_id`),
    FOREIGN KEY (`caminho_id`) REFERENCES `caminhos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de módulos
CREATE TABLE `modulos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `curso_id` INT(11) NOT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` VARCHAR(255) DEFAULT NULL,
    `ordem` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `curso_id` (`curso_id`),
    FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de lições
CREATE TABLE `licoes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `modulo_id` INT(11) NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `tipo` VARCHAR(50) DEFAULT NULL,
    `conteudo` TEXT DEFAULT NULL,
    `xp_recompensa` INT(11) DEFAULT 0,
    `ordem` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `modulo_id` (`modulo_id`),
    FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de conquistas dos alunos
CREATE TABLE `aluno_conquistas` (
    `aluno_id` INT(11) NOT NULL,
    `conquista_id` INT(11) NOT NULL,
    `data_conquista` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`aluno_id`,`conquista_id`),
    KEY `conquista_id` (`conquista_id`),
    FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`conquista_id`) REFERENCES `conquistas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de progresso nas lições
CREATE TABLE `progresso_licoes` (
    `aluno_id` INT(11) NOT NULL,
    `licao_id` INT(11) NOT NULL,
    `concluida` TINYINT(1) DEFAULT 0,
    `data_conclusao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`aluno_id`,`licao_id`),
    KEY `licao_id` (`licao_id`),
    FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`licao_id`) REFERENCES `licoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de notificações
CREATE TABLE `notificacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `responsavel_id` INT NOT NULL,
    `aluno_id` INT NOT NULL,
    `mensagem` TEXT NOT NULL,
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `lida` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`responsavel_id`) REFERENCES `responsaveis`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`aluno_id`) REFERENCES `alunos`(`id`) ON DELETE CASCADE,
    INDEX `idx_responsavel_data` (`responsavel_id`, `data_criacao` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reabilita verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- POPULAÇÃO DE DADOS INICIAIS
-- ============================================================================

-- Inserir caminhos (trilhas)
INSERT INTO `caminhos` (`nome`, `descricao`) VALUES
('Primeiros Passos', 'Lógica com Blocos Coloridos'),
('Criador de Jogos', 'Desenvolvimento de Games Simples'),
('Mago da Web', 'Criação de Sites e Apps');

-- Inserir cursos
INSERT INTO `cursos` (`nome_curso`, `descricao`, `duracao`, `caminho_id`) VALUES
('Iniciante - Primeiros Passos', 'Blocos, Sequências e Loops', 30, 1),
('Intermediário - Criador de Jogos', 'Variáveis, Condicionais e Colisões', 45, 2),
('Avançado - Mago da Web', 'HTML, CSS e JavaScript', 60, 3);

-- Inserir módulos para Iniciante (curso_id = 1)
INSERT INTO `modulos` (`curso_id`, `nome`, `descricao`, `ordem`) VALUES
(1, 'Módulo 1: Descobrindo os Blocos Mágicos', 'Aprenda sobre blocos de movimento', 1),
(1, 'Módulo 2: Loops e Repetições', 'Repita ações facilmente', 2),
(1, 'Módulo 3: Seu Primeiro Projeto', 'Crie seu primeiro programa', 3);

-- Inserir módulos para Intermediário (curso_id = 2)
INSERT INTO `modulos` (`curso_id`, `nome`, `descricao`, `ordem`) VALUES
(2, 'Módulo 1: Fundamentos de Movimento', 'Game loop e colisões', 1),
(2, 'Módulo 2: Variáveis e Estados', 'Armazene informações', 2),
(2, 'Módulo 3: Seu Primeiro Jogo', 'Crie um jogo completo', 3);

-- Inserir módulos para Avançado (curso_id = 3)
INSERT INTO `modulos` (`curso_id`, `nome`, `descricao`, `ordem`) VALUES
(3, 'Módulo 1: HTML Básico', 'Estrutura de páginas web', 1),
(3, 'Módulo 2: Estilizando com CSS', 'Deixe tudo bonito', 2),
(3, 'Módulo 3: Interatividade com JavaScript', 'Torne sua página viva', 3);

-- Inserir lições para o primeiro módulo de Iniciante (modulo_id = 1)
INSERT INTO `licoes` (`modulo_id`, `titulo`, `tipo`, `xp_recompensa`, `ordem`) VALUES
(1, 'Lição 1.1: O Bloco "Mover" e a Coordenada X', 'pratica', 10, 1),
(1, 'Lição 1.2: Bloco "Repetir": Criando Loops Simples', 'pratica', 15, 2),
(1, 'Lição 1.3: Desafio Final do Módulo', 'desafio', 20, 3);

