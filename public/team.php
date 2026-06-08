<?php
require_once(__DIR__ . '/../data/base.php');

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: register");
    exit;
}

$userLoggedIn = $_SESSION['userLoggedIn'];
$user_id      = (int) $_SESSION['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: team");
        exit;
    }
}

// Génère un code de 6 chars sans caractères ambigus, unique en base
function generate_team_code(mysqli $db): string {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $s = mysqli_prepare($db, "SELECT id FROM teams WHERE code = ?");
        mysqli_stmt_bind_param($s, "s", $code);
        mysqli_stmt_execute($s);
        $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    } while ($exists);
    return $code;
}

function fmt_s(int $s): string {
    return sprintf("%dm%02ds", intdiv($s, 60), $s % 60);
}

$error = '';

// --- Créer une équipe ---
if (isset($_POST['create_team'])) {
    $name = trim($_POST['team_name'] ?? '');
    if ($name === '') {
        $error = "Veuillez entrer un nom d'équipe.";
    } elseif (mb_strlen($name) > 50) {
        $error = "Le nom est trop long (50 caractères max).";
    } else {
        $code = generate_team_code($connexion);
        $stmt = mysqli_prepare($connexion,
            "INSERT INTO teams (code, name, created_by) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $code, $name, $user_id);
        mysqli_stmt_execute($stmt);
        $_SESSION['team_id'] = mysqli_insert_id($connexion);
    }
}

// --- Rejoindre une équipe ---
if (isset($_POST['join_team'])) {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    if (!preg_match('/^[A-Z0-9]{6}$/', $code)) {
        $error = "Le code doit contenir exactement 6 caractères alphanumériques.";
    } else {
        $stmt = mysqli_prepare($connexion, "SELECT id FROM teams WHERE code = ?");
        mysqli_stmt_bind_param($stmt, "s", $code);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$row) {
            $error = "Code inconnu. Vérifiez le code et réessayez.";
        } else {
            $_SESSION['team_id'] = (int) $row['id'];
        }
    }
}

// --- Quitter l'équipe ---
if (isset($_POST['leave_team'])) {
    unset($_SESSION['team_id']);
    header("Location: team");
    exit;
}

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

// Toujours vérifier la partie en cours (utile pour l'UI et les POST)
$stmt = mysqli_prepare($connexion,
    "SELECT id, enigme_courante FROM progression
     WHERE user_id = ? AND statut = 'en_cours' LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$partie_en_cours = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// --- Reprendre la partie en cours (rattachée à l'équipe) ---
if (isset($_POST['play_team']) && !empty($_SESSION['team_id'])) {
    $team_id = (int) $_SESSION['team_id'];

    if ($partie_en_cours) {
        $partie_id = (int) $partie_en_cours['id'];
        $enigme    = (int) $partie_en_cours['enigme_courante'];
        $stmt2 = mysqli_prepare($connexion,
            "UPDATE progression SET team_id = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "ii", $team_id, $partie_id);
        mysqli_stmt_execute($stmt2);
    } else {
        $stmt = mysqli_prepare($connexion,
            "INSERT INTO progression (user_id, enigme_courante, statut, team_id) VALUES (?, 0, 'en_cours', ?)");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $team_id);
        mysqli_stmt_execute($stmt);
        $partie_id = mysqli_insert_id($connexion);
        $enigme    = 0;
    }

    $_SESSION['partie_id']       = $partie_id;
    $_SESSION['enigme_courante'] = $enigme;
    header("Location: " . ($enigmes_map[$enigme] ?? 'debut'));
    exit;
}

// --- Nouvelle partie en équipe (abandonne l'existante) ---
if (isset($_POST['new_team_game']) && !empty($_SESSION['team_id'])) {
    $team_id = (int) $_SESSION['team_id'];

    if ($partie_en_cours) {
        $stmt = mysqli_prepare($connexion, "DELETE FROM progression WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $partie_en_cours['id']);
        mysqli_stmt_execute($stmt);
    }
    $stmt = mysqli_prepare($connexion,
        "INSERT INTO progression (user_id, enigme_courante, statut, team_id) VALUES (?, 0, 'en_cours', ?)");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $team_id);
    mysqli_stmt_execute($stmt);
    $_SESSION['partie_id']       = mysqli_insert_id($connexion);
    $_SESSION['enigme_courante'] = 0;
    header("Location: debut");
    exit;
}

// --- Charger l'équipe actuelle ---
$current_team   = null;
$team_members   = [];
$current_team_id = (int) ($_SESSION['team_id'] ?? 0);

if ($current_team_id) {
    $stmt = mysqli_prepare($connexion,
        "SELECT id, code, name FROM teams WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $current_team_id);
    mysqli_stmt_execute($stmt);
    $current_team = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$current_team) {
        unset($_SESSION['team_id']);
    } else {
        // Membres et leur état (dernière partie liée à l'équipe)
        $stmt = mysqli_prepare($connexion,
            "SELECT u.username,
                    p.statut,
                    p.enigme_courante,
                    p.score
             FROM progression p
             JOIN users u ON u.id = p.user_id
             WHERE p.team_id = ?
             ORDER BY p.statut DESC, p.score ASC, p.date_debut ASC");
        mysqli_stmt_bind_param($stmt, "i", $current_team_id);
        mysqli_stmt_execute($stmt);
        $team_members = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Équipe – Escape Game</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <style>
        .team-container { max-width: 560px; margin: 2rem auto; }
        .team-container h1 { text-align: center; margin-bottom: 1.5rem; }
        .team-box {
            background: rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .team-code-display {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 0.25em;
            text-align: center;
            padding: 0.6rem;
            background: rgba(240,192,64,0.15);
            border-radius: 8px;
            margin: 0.8rem 0 1.2rem;
        }
        .team-hint { text-align: center; opacity: 0.65; font-size: 0.85rem; margin-bottom: 1.4rem; }
        .member-table { width: 100%; border-collapse: collapse; margin-bottom: 1.2rem; }
        .member-table th, .member-table td {
            padding: 0.45rem 0.8rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: left;
        }
        .member-table th { opacity: 0.55; font-size: 0.82rem; text-transform: uppercase; }
        .badge-done  { color: #4caf50; font-weight: bold; }
        .badge-inprogress { opacity: 0.7; }
        .btn-row { display: flex; gap: 0.8rem; justify-content: center; flex-wrap: wrap; margin-top: 0.8rem; }
        .btn { display:inline-block; padding: 0.65rem 1.3rem; border-radius:8px;
               border:none; font:inherit; font-weight:bold; cursor:pointer;
               text-decoration:none; transition: opacity .2s; }
        .btn:hover { opacity: 0.8; }
        .btn-play   { background: #4caf50; color: #fff; }
        .btn-leave  { background: rgba(255,255,255,0.1); color: inherit; }
        .btn-back   { background: rgba(255,255,255,0.1); color: inherit; }
        .forms-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 480px) { .forms-grid { grid-template-columns: 1fr; } }
        .form-card {
            background: rgba(255,255,255,0.06);
            border-radius: 10px;
            padding: 1.4rem;
        }
        .form-card h2 { margin: 0 0 1rem; font-size: 1.1rem; }
        .form-card input[type=text] {
            width: 100%; box-sizing: border-box;
            padding: 0.55rem 0.8rem; border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
            color: inherit; font: inherit;
            margin-bottom: 0.8rem;
        }
        .error { color: #f44336; text-align: center; margin-bottom: 1rem; }
        .divider { text-align: center; opacity: 0.4; margin: 1.5rem 0 1rem; font-size: 0.9rem; }
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

<div class="mainContent team-container">
    <h1>Équipes</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($current_team): ?>

        <!-- Vue : dans une équipe -->
        <div class="team-box">
            <h2 style="text-align:center;margin:0 0 0.3rem">
                <?= htmlspecialchars($current_team['name'], ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <p class="team-hint">Partagez ce code à vos coéquipiers :</p>
            <div class="team-code-display"><?= htmlspecialchars($current_team['code'], ENT_QUOTES, 'UTF-8') ?></div>

            <?php if (!empty($team_members)): ?>
                <table class="member-table">
                    <thead><tr><th>Joueur</th><th>État</th></tr></thead>
                    <tbody>
                    <?php foreach ($team_members as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['username'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                            <?php if ($m['statut'] === 'termine' && $m['score'] > 0): ?>
                                <span class="badge-done">Terminé — <?= fmt_s((int) $m['score']) ?></span>
                            <?php else: ?>
                                <span class="badge-inprogress">Étape <?= (int) $m['enigme_courante'] ?>/8</span>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center;opacity:.6">Aucun joueur n'a encore commencé.</p>
            <?php endif; ?>

            <div class="btn-row">
                <?php if ($partie_en_cours): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <div style="text-align:center">
                            <button type="submit" name="play_team" class="btn btn-play">▶ Reprendre (étape <?= (int) $partie_en_cours['enigme_courante'] ?>/8)</button>
                            <br>
                            <button type="submit" name="new_team_game" style="background:none;border:none;font:inherit;color:inherit;cursor:pointer;font-size:.8rem;opacity:.55;text-decoration:underline;margin-top:.4rem">Recommencer depuis le début</button>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" name="play_team" class="btn btn-play">▶ Jouer avec mon équipe</button>
                    </form>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" name="leave_team" class="btn btn-leave">Quitter l'équipe</button>
                </form>
            </div>
        </div>

        <div class="divider">— ou —</div>
        <div style="text-align:center">
            <a href="browse" class="btn btn-back">← Retour au menu</a>
        </div>

    <?php else: ?>

        <!-- Vue : sans équipe — créer ou rejoindre -->
        <div class="forms-grid">
            <div class="form-card">
                <h2>Créer une équipe</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="text" name="team_name" placeholder="Nom de l'équipe" maxlength="50" required>
                    <button type="submit" name="create_team" class="btn btn-play" style="width:100%">Créer</button>
                </form>
            </div>

            <div class="form-card">
                <h2>Rejoindre une équipe</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="text" name="code" placeholder="Code (ex: AB3X9Y)" maxlength="6"
                           style="text-transform:uppercase;letter-spacing:0.15em" required>
                    <button type="submit" name="join_team" class="btn btn-play" style="width:100%">Rejoindre</button>
                </form>
            </div>
        </div>

        <div class="divider">— ou —</div>
        <div style="text-align:center">
            <a href="browse" class="btn btn-back">← Retour (jouer seul)</a>
        </div>

    <?php endif; ?>
</div>

</body>
</html>
