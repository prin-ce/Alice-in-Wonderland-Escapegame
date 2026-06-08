<?php
require_once(__DIR__ . "/../../data/base.php");

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../register");
    exit;
}

$userLoggedIn = $_SESSION['userLoggedIn'];
$user_id      = $_SESSION['user_id']        ?? null;
$partie_id    = $_SESSION['last_partie_id'] ?? null;

$duree_s   = null;
$rang      = null;
$total     = null;
$team_name = null;

if ($partie_id && $user_id) {
    // Durée, score et équipe de la partie qui vient de se terminer
    $stmt = mysqli_prepare($connexion,
        "SELECT p.score, TIMESTAMPDIFF(SECOND, p.date_debut, p.date_fin) AS duree_s,
                t.name AS team_name
         FROM progression p
         LEFT JOIN teams t ON t.id = p.team_id
         WHERE p.id = ? AND p.user_id = ? AND p.statut = 'termine'");
    mysqli_stmt_bind_param($stmt, "ii", $partie_id, $user_id);
    mysqli_stmt_execute($stmt);
    $partie = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($partie && $partie['score'] > 0) {
        $score     = (int) $partie['score'];
        $duree_s   = (int) $partie['duree_s'];
        $team_name = $partie['team_name'];

        // Rang : combien ont fait mieux (temps plus court = score plus bas) ?
        $stmt2 = mysqli_prepare($connexion,
            "SELECT COUNT(*) AS mieux FROM progression
             WHERE statut = 'termine' AND score > 0 AND score < ?");
        mysqli_stmt_bind_param($stmt2, "i", $score);
        mysqli_stmt_execute($stmt2);
        $rang = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['mieux'] + 1;

        // Total de joueurs ayant terminé
        $stmt3 = mysqli_prepare($connexion,
            "SELECT COUNT(*) AS total FROM progression WHERE statut = 'termine' AND score > 0");
        mysqli_stmt_execute($stmt3);
        $total = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3))['total'];
    }
}

// Formatage de la durée en "X min YY s"
function fmt_duree(int $s): string {
    $min = intdiv($s, 60);
    $sec = $s % 60;
    return $min > 0
        ? "{$min} min " . str_pad($sec, 2, '0') . " s"
        : "{$sec} s";
}

// Suffixe ordinal français
function ordinal(int $n): string {
    return $n === 1 ? "1<sup>er</sup>" : "{$n}<sup>ème</sup>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Escape Game – Fin</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/fin.css">
    <style>
        #result-box {
            text-align: center;
            margin: 2rem auto;
            max-width: 480px;
            background: rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 2rem;
        }
        #result-box .duree {
            font-size: 2.4rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        #result-box .rang {
            font-size: 1.2rem;
            margin: 0.5rem 0 1.5rem;
            opacity: 0.85;
        }
        .fin-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .fin-actions a {
            display: inline-block;
            padding: 0.7rem 1.4rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: opacity .2s;
        }
        .fin-actions a:hover { opacity: 0.8; }
        .btn-leaderboard { background: #f0c040; color: #1a1a2e; }
        .btn-rejouer     { background: #4caf50; color: #fff; }
    </style>
</head>
<body>

    <div id="HeadBan">
        <p id="NomJeu">Escape Game : Alice au pays des merveilles</p>
        <span id="playerName"><?php echo htmlspecialchars($userLoggedIn, ENT_QUOTES, 'UTF-8'); ?></span>
        <div id="Player">
            <a href="../profil"><img id="ppPlayer" src="../images/icons/pp.png"></a>
        </div>
    </div><br>

    <div id="MainBox">
        <p id="text">Félicitations, vous avez terminé tous les niveaux !</p>

        <?php if ($duree_s !== null && $rang !== null): ?>
        <div id="result-box">
            <div class="duree"><?= fmt_duree($duree_s) ?></div>
            <div class="rang">
                Vous êtes <?= ordinal($rang) ?> sur <?= $total ?> joueur<?= $total > 1 ? 's' : '' ?>
            </div>
            <?php if ($team_name !== null): ?>
            <div style="opacity:.75;font-size:.9rem;margin-bottom:.8rem">
                Équipe : <strong><?= htmlspecialchars($team_name, ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <?php endif; ?>
            <div class="fin-actions">
                <a href="../leaderboard" class="btn-leaderboard">🏆 Voir le classement</a>
                <a href="../browse"      class="btn-rejouer">↩ Rejouer</a>
            </div>
        </div>
        <?php else: ?>
        <div class="fin-actions" style="margin-top:1.5rem">
            <a href="../browse" class="btn-rejouer">↩ Retour à l'accueil</a>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
