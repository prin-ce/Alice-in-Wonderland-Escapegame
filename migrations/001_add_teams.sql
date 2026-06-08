-- Migration 001 : ajout du mode équipe (multijoueur asynchrone)
-- À exécuter UNE SEULE FOIS sur une base existante.
-- (Inutile si vous repartez d'un sql_projet.sql fraîchement importé)

USE escapegame;

CREATE TABLE IF NOT EXISTS teams
(
  id         INT         PRIMARY KEY AUTO_INCREMENT,
  code       CHAR(6)     NOT NULL UNIQUE COMMENT 'Code de rejointe partageable',
  name       VARCHAR(50) NOT NULL,
  created_by INT         NOT NULL,
  created_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE progression
  ADD COLUMN team_id INT NULL DEFAULT NULL COMMENT 'NULL = solo'
      AFTER user_id,
  ADD CONSTRAINT fk_progression_team
      FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL;
