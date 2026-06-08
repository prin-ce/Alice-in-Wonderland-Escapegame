<?php
require_once(__DIR__ . '/../data/base.php');

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: register");
    exit;
}

$userLoggedIn    = $_SESSION['userLoggedIn'];
$current_user_id = $_SESSION['user_id'] ?? null;

// Meilleure partie par joueur (temps le plus court = score le plus bas)
$sql = "SELECT u.id AS user_id,
               u.username,
               MIN(p.score) AS meilleur_score,
               TIMESTAMPDIFF(SECOND, p.date_debut, p.date_fin) AS meilleur_temps
        FROM progression p
        JOIN users u ON p.user_id = u.id
        WHERE p.statut = 'termine' AND p.score > 0
          AND p.id = (
              SELECT p2.id FROM progression p2
              WHERE p2.user_id = p.user_id AND p2.statut = 'termine' AND p2.score > 0
              ORDER BY p2.score ASC
              LIMIT 1
          )
        GROUP BY u.id, u.username, meilleur_temps
        ORDER BY meilleur_score ASC
        LIMIT 10";

$result     = mysqli_query($connexion, $sql);
$classement = [];
while ($row = mysqli_fetch_assoc($result)) {
    $classement[] = $row;
}

// Classement par équipe — score moyen des membres ayant terminé
$sql_teams = "SELECT t.id AS team_id,
                     t.name AS team_name,
                     t.code,
                     COUNT(p.id)   AS membres_termines,
                     ROUND(AVG(p.score)) AS score_moyen
              FROM teams t
              JOIN progression p ON p.team_id = t.id
                                 AND p.statut = 'termine'
                                 AND p.score > 0
              GROUP BY t.id, t.name, t.code
              ORDER BY score_moyen ASC
              LIMIT 10";

$result_teams   = mysqli_query($connexion, $sql_teams);
$classement_eq  = [];
while ($row = mysqli_fetch_assoc($result_teams)) {
    $classement_eq[] = $row;
}

$current_team_id = $_SESSION['team_id'] ?? null;

function format_temps(int $secondes): string {
    $min = intdiv($secondes, 60);
    $sec = $secondes % 60;
    return sprintf("%d min %02d s", $min, $sec);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Classement – Escape Game</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <style>
        .leaderboard-container { max-width: 600px; margin: 2rem auto; }
        .leaderboard-container h1 { text-align: center; margin-bottom: 1.5rem; }
        .leaderboard-table { width: 100%; border-collapse: collapse; }
        .leaderboard-table th,
        .leaderboard-table td { padding: 0.6rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .leaderboard-table th { opacity: 0.6; font-size: 0.85rem; text-transform: uppercase; }
        .current-player { background: rgba(240,192,64,0.15); font-weight: bold; }
        .text-center { text-align: center; }
        .retour-menu { margin-top: 1.5rem; }
        .retour-menu a { opacity: 0.7; text-decoration: none; }
        .retour-menu a:hover { opacity: 1; }
    </style>
</head>
<body>

    <div id="HeadBan">
        <p id="NomJeu">Escape Game : Alice au pays des merveilles</p>
        <span id="playerName"><?= htmlspecialchars($userLoggedIn, ENT_QUOTES, 'UTF-8') ?></span>
        <div id="Player">
            <a href="profil"><img id="ppPlayer" src="images/icons/pp.png"></a>
        </div>
    </div><br>

    <div class="mainContent leaderboard-container">
        <h1>🏆 Top 10 des joueurs</h1>

        <?php if (empty($classement)): ?>
            <p class="text-center">Aucune partie terminée pour le moment.</p>
        <?php else: ?>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Joueur</th>
                        <th>Meilleur temps</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $medals = ['🥇', '🥈', '🥉'];
                foreach ($classement as $i => $ligne):
                    $isMe = ($ligne['user_id'] == $current_user_id);
                ?>
                    <tr class="<?= $isMe ? 'current-player' : '' ?>">
                        <td><?= $medals[$i] ?? ($i + 1) ?></td>
                        <td><?= htmlspecialchars($ligne['username'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= format_temps((int) $ligne['meilleur_score']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h1 style="margin-top:2.5rem">👥 Top 10 des équipes</h1>
        <p class="text-center" style="opacity:.65;font-size:.9rem;margin-bottom:1rem">
            Score = temps moyen de tous les membres ayant terminé
        </p>

        <?php if (empty($classement_eq)): ?>
            <p class="text-center">Aucune équipe n'a encore terminé.</p>
        <?php else: ?>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Équipe</th>
                        <th>Code</th>
                        <th>Membres</th>
                        <th>Temps moyen</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $medals = ['🥇', '🥈', '🥉'];
                foreach ($classement_eq as $i => $eq):
                    $isMyTeam = ($eq['team_id'] == $current_team_id);
                ?>
                    <tr class="<?= $isMyTeam ? 'current-player' : '' ?>">
                        <td><?= $medals[$i] ?? ($i + 1) ?></td>
                        <td><?= htmlspecialchars($eq['team_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="font-family:monospace;letter-spacing:.1em"><?= htmlspecialchars($eq['code'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-center"><?= (int) $eq['membres_termines'] ?></td>
                        <td><?= format_temps((int) $eq['score_moyen']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p class="text-center retour-menu">
            <a href="browse">← Retour au menu</a>
        </p>
    </div>

</body>
</html>
