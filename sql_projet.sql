--
-- Base de données : AliceInWonderland
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

DROP DATABASE IF EXISTS AliceInWonderland;

CREATE DATABASE IF NOT EXISTS AliceInWonderland
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE AliceInWonderland;

-- --------------------------------------------------------

--
-- Structure de la table users
--

CREATE TABLE users
(
  id int PRIMARY KEY AUTO_INCREMENT,
  username varchar(25) NOT NULL,
  firstName varchar(50) NOT NULL,
  lastName varchar(50) NOT NULL,
  email varchar(200) NOT NULL,
  password varchar(255) NOT NULL,
  signUpDate date NOT NULL,
  profilePic varchar(500) NOT NULL
) ENGINE=InnoDB;

--
-- Structure de la table teams (multijoueur asynchrone)
--

CREATE TABLE teams
(
  id         INT         PRIMARY KEY AUTO_INCREMENT,
  code       CHAR(6)     NOT NULL UNIQUE COMMENT 'Code de rejointe partageable',
  name       VARCHAR(50) NOT NULL,
  created_by INT         NOT NULL,
  created_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--
-- Structure de la table progression
--

CREATE TABLE progression
(
  id              INT            PRIMARY KEY AUTO_INCREMENT,
  user_id         INT            NOT NULL,
  team_id         INT            NULL DEFAULT NULL COMMENT 'NULL = solo',
  statut          ENUM('en_cours', 'termine') NOT NULL DEFAULT 'en_cours',
  enigme_courante TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Étape actuelle de 0 (début) à 7 (énigmes terminées)',
  score           INT            NOT NULL DEFAULT 0,
  date_debut      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_fin        DATETIME       NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
) ENGINE=InnoDB;


