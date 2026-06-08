# Escape Game — Alice au Pays des Merveilles

Escape game pédagogique en PHP/MySQL sur le thème d'Alice au Pays des Merveilles. Les joueurs résolvent 7 énigmes de programmation (PHP, CSS, JavaScript) pour s'échapper du Pays des Merveilles.

---

## Fonctionnalités

- Inscription / connexion avec hachage bcrypt des mots de passe
- Protection CSRF sur tous les formulaires POST
- Progression persistante en base de données avec reprise de partie
- Mode "Nouvelle partie" : recommencer depuis le début sans perdre son compte
- Multijoueur asynchrone par équipes : créer une équipe avec un code à 6 caractères, inviter des joueurs, jouer chacun de son côté
- Classement individuel et par équipe (score basé sur le temps)
- 7 énigmes de difficulté progressive : CSS, PHP, JavaScript, DevTools

---

## Stack technique

- **Back-end** : PHP 8+ (architecture MVC, requêtes préparées MySQLi)
- **Base de données** : MySQL via Laragon
- **Front-end** : HTML/CSS, JavaScript vanilla, jQuery 3.7.1 (Snake uniquement)
- **Serveur local** : Laragon (Apache + MySQL)

---

## Installation

### Prérequis

- [Laragon](https://laragon.org/) (ou tout serveur Apache + PHP 8+ + MySQL)
- MySQL accessible sur `localhost`

### Étapes

1. Cloner ou copier le projet dans le dossier `www` de Laragon :
   ```
   C:\laragon\www\AliceInWonderland\
   ```

2. Créer la base de données et les tables :
   ```bash
   mysql -u root -p < sql_projet.sql
   ```

3. Appliquer la migration multijoueur si la base existait avant :
   ```bash
   mysql -u root -p AliceInWonderland < migrations/001_add_teams.sql
   ```

4. Vérifier la configuration de connexion dans `data/base.php` :
   ```php
   $connexion = mysqli_connect("localhost", "root", "", "AliceInWonderland");
   ```

5. Pointer le virtual host sur `public/` — dans `sites-enabled/auto.AliceInWonderland.test.conf` :
   ```apache
   define ROOT "C:/laragon/www/AliceInWonderland/public"
   ```

6. Redémarrer Apache (clic droit icône Laragon → Apache → Reload).

7. Accéder au jeu : `http://AliceInWonderland.test`

---

## Structure du projet

```
AliceInWonderland/
├── public/               ← DocumentRoot (AliceInWonderland.test)
│   ├── .htaccess         — Sécurité : interdit l'accès aux fichiers sensibles
│   ├── index.php         — Point d'entrée
│   ├── browse.php        — Page d'accueil après connexion
│   ├── register.php      — Connexion / inscription
│   ├── team.php          — Gestion des équipes
│   ├── leaderboard.php   — Classements individuel et équipes
│   ├── profil.php        — Profil utilisateur
│   ├── close_account.php — Suppression de compte
│   ├── logout.php        — Déconnexion + destruction session
│   ├── debut.html        — Vidéo d'introduction
│   ├── enigmes/          — Dispatchers PHP (appellent EnigmeController)
│   │   ├── EnigmeTaille.php
│   │   ├── EnigmeMiam.php
│   │   ├── EnigmeTableau.php
│   │   ├── EnigmeCarte.php
│   │   ├── snake.php
│   │   ├── EnigmeFillette.php
│   │   ├── EnigmeLapin.php
│   │   └── Fin.php
│   ├── transitions/      — Pages narratives HTML entre les énigmes
│   ├── css/              — Feuilles de style
│   ├── js/               — JavaScript des énigmes
│   │   └── transitions/  — JS des animations de transition
│   ├── audio/            — Effets sonores
│   ├── images/           — Images et avatars
│   ├── font/             — Polices personnalisées
│   └── video/            — Vidéos d'intro/fin
├── controllers/
│   └── EnigmeController.php  — Logique de validation des 7 énigmes
├── views/
│   ├── layout/
│   │   ├── header.php    — En-tête commune à toutes les énigmes
│   │   └── footer.php
│   └── enigmes/          — Templates HTML de chaque énigme
├── classes/
│   ├── User.php          — Lecture profil utilisateur (lazy-loading)
│   ├── Fonctions.php     — Validation et insertion à l'inscription
│   └── Erreurs.php       — Messages d'erreur
├── data/                 — Code PHP non accessible depuis le web
│   ├── base.php          — Connexion MySQL + session_start()
│   ├── connexion.php     — Traitement login
│   ├── inscription.php   — Traitement inscription
│   ├── progression.php   — Contrôle d'accès + avancer_enigme()
│   └── updateDetails.php — Mise à jour profil et mot de passe
├── migrations/
│   └── 001_add_teams.sql — Ajout des tables teams + colonne team_id
├── sql_projet.sql        — Schéma complet de la base de données
├── SOLUTIONS.md          — Solutions des énigmes + alternatives
└── README.md
```

---

## Ordre des énigmes

| # | Nom | Concept | Difficulté |
|---|-----|---------|------------|
| 1 | Taille | Propriétés CSS `height` et unités | Facile |
| 2 | Miam | Opérateur de concaténation PHP (`.`) | Facile-Moyen |
| 3 | Tableau | Indexation 0-based des tableaux PHP | Facile-Moyen |
| 4 | Carte | Fonction PHP `rand(min, max)` | Moyen |
| 5 | Snake | Trivia CSS/PHP/JS — jeu d'arcade | Moyen |
| 6 | Fillette | Sélecteurs CSS (`#` vs `.`) et `visibility`/`opacity` | Moyen-Difficile |
| 7 | Lapin | Inspection console navigateur (DevTools) | Difficile |

Les solutions détaillées et les énigmes alternatives sont dans [SOLUTIONS.md](SOLUTIONS.md).

---

## Multijoueur par équipes

1. Un joueur crée une équipe depuis `team.php` et obtient un code à 6 caractères
2. Les autres joueurs rejoignent avec ce code
3. Chaque membre joue de façon indépendante à son rythme
4. Le classement par équipes sur `leaderboard.php` agrège les scores des membres ayant terminé

---

## Sécurité

- Mots de passe hachés avec `password_hash()` (bcrypt)
- Toutes les requêtes SQL utilisent des requêtes préparées (MySQLi)
- Protection CSRF sur tous les formulaires POST (`bin2hex(random_bytes(32))` + `hash_equals()`)
- `session_regenerate_id(true)` à la connexion et à l'inscription (protection contre la fixation de session)
- Déconnexion complète : vidage `$_SESSION`, expiration du cookie, `session_destroy()`
- jQuery chargé depuis CDN avec hash SRI

---

## Crédits

Projet réalisé dans le cadre d'un cours de développement web à l'Institut des sciences du Digital, Management et Cognition (IDMC) de Nancy.

---

## Licence

Distribué sous licence [MIT](LICENSE).
