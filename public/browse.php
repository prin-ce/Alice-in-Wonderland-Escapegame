<?php
require_once(__DIR__ . '/../data/base.php');

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: register");
    exit;
}

$userLoggedIn = $_SESSION['userLoggedIn'];

// Fallback pour les sessions ouvertes avant que connexion.php stocke user_id
if (!isset($_SESSION['user_id'])) {
    $s = mysqli_prepare($connexion, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($s, "s", $userLoggedIn);
    mysqli_stmt_execute($s);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    if (!$r) { header("Location: register.php"); exit; }
    $_SESSION['user_id'] = (int) $r['id'];
}
$user_id = (int) $_SESSION['user_id'];

// Toujours vérifier s'il existe une partie en cours (utile pour l'UI et les POST)
$stmt = mysqli_prepare($connexion,
    "SELECT id, enigme_courante FROM progression
     WHERE user_id = ? AND statut = 'en_cours' LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$partie_en_cours = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$enigmes_map = [
    0 => 'debut',
    1 => 'enigmes/EnigmeTaille',
    2 => 'enigmes/EnigmeMiam',
    3 => 'enigmes/EnigmeTableau',
    4 => 'enigmes/EnigmeCarte',
    5 => 'enigmes/snake',
    6 => 'enigmes/EnigmeFillette',
    7 => 'enigmes/EnigmeLapin',
];

// Reprendre la partie existante
if (isset($_POST['play_solo'])) {
    if ($partie_en_cours) {
        $partie_id = (int) $partie_en_cours['id'];
        $enigme    = (int) $partie_en_cours['enigme_courante'];
    } else {
        $stmt = mysqli_prepare($connexion,
            "INSERT INTO progression (user_id, enigme_courante, statut) VALUES (?, 0, 'en_cours')");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $partie_id = mysqli_insert_id($connexion);
        $enigme    = 0;
    }
    $_SESSION['partie_id']       = $partie_id;
    $_SESSION['enigme_courante'] = $enigme;
    header("Location: " . ($enigmes_map[$enigme] ?? 'debut'));
    exit;
}

// Abandonner la partie en cours et en créer une nouvelle
if (isset($_POST['new_game_solo'])) {
    if ($partie_en_cours) {
        $stmt = mysqli_prepare($connexion, "DELETE FROM progression WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $partie_en_cours['id']);
        mysqli_stmt_execute($stmt);
    }
    $stmt = mysqli_prepare($connexion,
        "INSERT INTO progression (user_id, enigme_courante, statut) VALUES (?, 0, 'en_cours')");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $partie_id = mysqli_insert_id($connexion);
    $_SESSION['partie_id']       = $partie_id;
    $_SESSION['enigme_courante'] = 0;
    header("Location: debut");
    exit;
}

echo "<script>userLoggedIn = '" . htmlspecialchars($userLoggedIn, ENT_QUOTES, 'UTF-8') . "';</script>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Escape Game</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <style>
        .new-game-link {
            display: block;
            margin-top: 0.4rem;
            font-size: 0.78rem;
            opacity: 0.55;
            cursor: pointer;
            text-decoration: underline;
            background: none;
            border: none;
            color: inherit;
            font-family: inherit;
        }
        .new-game-link:hover { opacity: 0.85; }
        .resume-label {
            display: block;
            font-size: 0.75rem;
            opacity: 0.6;
            margin-top: 0.2rem;
        }
    </style>
</head>
<body>

    <div id="HeadBan">
        <p id="NomJeu">Escape Game : Alice au pays des merveilles</p>
        <span id="playerName"><?php echo htmlspecialchars($userLoggedIn, ENT_QUOTES, 'UTF-8'); ?></span>
        <div id="Player">
            <a href="profil"><img id="ppPlayer" src="images/icons/pp.png"></a>
        </div>
    </div>

    <div class="mainContent">

        <div class="label">Que voulez-vous faire ?</div>

        <ul class="actions">
            <li class="play">
                <div>
                    <a href="team"><img src="images/icons/groupe.png">Joindre une équipe</a>
                </div>
            </li>
            <li class="play">
                <div>
                    <?php if ($partie_en_cours): ?>
                        <form method="POST" style="display:inline;">
                            <button type="submit" name="play_solo" style="background:none;border:none;padding:0;font:inherit;color:inherit;cursor:pointer;">
                                <img src="images/icons/solo.png">Reprendre la partie
                            </button>
                            <span class="resume-label">Étape <?= (int) $partie_en_cours['enigme_courante'] ?>/8</span>
                        </form>
                        <form method="POST" style="display:inline;">
                            <button type="submit" name="new_game_solo" class="new-game-link">Recommencer depuis le début</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" style="display:inline;">
                            <button type="submit" name="play_solo" style="background:none;border:none;padding:0;font:inherit;color:inherit;cursor:pointer;">
                                <img src="images/icons/solo.png">Jouer seul
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </li>
            <li class="play">
                <div>
                    <a href="leaderboard"><img src="images/icons/trophy.png">Classement</a>
                </div>
            </li>
        </ul>

        <ul class="actions">
            <li class="managment">
                <div>
                    <a href="logout"><img src="images/icons/logout.png" style="width:70px"></a>
                </div>
            </li>
        </ul>

    </div>

</body>
</html>
