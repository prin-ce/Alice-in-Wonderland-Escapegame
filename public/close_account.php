<?php
require_once(__DIR__ . "/../data/base.php");

if (!isset($_SESSION["userLoggedIn"]) || empty($_SESSION["userLoggedIn"])) {
    header("Location: register.php");
    exit;
}

$userLoggedIn = $_SESSION["userLoggedIn"];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation CSRF unifiée pour les deux boutons (même formulaire)
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        header("Location: close_account");
        exit;
    }

    if (isset($_POST['non'])) {
        header("Location: profil");
        exit;
    }

    if (isset($_POST['oui'])) {
        $stmt = mysqli_prepare($connexion, "DELETE FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $userLoggedIn);
        mysqli_stmt_execute($stmt);

        // Destruction complète de la session (même séquence que logout.php)
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();

        header("Location: register");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Profil – Suppression du compte</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

    <div class="settingsContainer column">
        <div class="formSection">
            <h2>Supprimer votre compte</h2>
            <p>Voulez-vous vraiment supprimer votre compte ?</p>
            <p>Cette action est irréversible</p>

            <form method="POST">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="submit" style="background-color:red" name="oui" value="Oui !">
                <input type="submit" name="non" value="Non !">
            </form>
        </div>
    </div>

</body>
</html>
