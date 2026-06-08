<?php
require_once(__DIR__ . "/../data/base.php");
require_once(__DIR__ . "/../classes/User.php");
require_once(__DIR__ . "/../classes/Fonctions.php");
require_once(__DIR__ . "/../classes/Erreurs.php");

// Initialiser les variables AVANT d'inclure updateDetails
$detailsMessage = "";
$passwordMessage = "";

// Vérifier la session AVANT tout
if (!isset($_SESSION["userLoggedIn"])) {
    header("Location: register");
    exit;
}

$userLoggedIn = $_SESSION["userLoggedIn"];

// Génération du token CSRF (un par session)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ----- Traitement des boutons de navigation (hors formulaires sensibles) -----
if (isset($_POST["closeAccountButton"])) {
    // Redirige vers close_account.php (qui a sa propre protection CSRF)
    header("Location: close_account");
    exit;
}

if (isset($_POST["returnButton"])) {
    header("Location: browse");
    exit;
}

// Inclusion du traitement des formulaires de mise à jour (vérification CSRF incluse)
require_once(__DIR__ . "/../data/updateDetails.php");
?>

<html>
<head>
    <title>Profil</title>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>

    <div class="settingsContainer column">

        <!-- Formulaire 1 : mise à jour des informations -->
        <div class="formSection">
            <form method="POST">
                <h2>Mes informations :</h2>

                <?php
                $user = new User($connexion, $userLoggedIn);

                $firstName = isset($_POST["firstName"]) ? $_POST["firstName"] : $user->getFirstName();
                $lastName  = isset($_POST["lastName"])  ? $_POST["lastName"]  : $user->getLastName();
                $email     = isset($_POST["email"])     ? $_POST["email"]     : $user->getEmail();
                ?>

                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="text" name="lastName"  value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="email" name="email"    value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="message">
                    <?php echo $detailsMessage; ?>
                </div>

                <input type="submit" name="saveDetailsButton" value="Mettre à jour">
            </form>
        </div>

        <!-- Formulaire 2 : changement de mot de passe -->
        <div class="formSection">
            <form method="POST">
                <h2>Modifier le mot de passe</h2>

                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <input type="password" name="oldPassword" placeholder="Ancien mot de passe">
                <input type="password" name="newPassword" placeholder="Nouveau mot de passe">
                <input type="password" name="newPassword2" placeholder="Confirmation">

                <div class="message">
                    <?php echo $passwordMessage; ?>
                </div>

                <input type="submit" name="savePasswordButton" value="Modifier">
            </form>
        </div>

        <!-- Formulaire 3 : navigation (pas de token nécessaire) -->
        <div class="fin">
            <form method="POST">
                <input type="submit" name="closeAccountButton" style="background-color: red" value="Supprimer">
                <input type="submit" name="returnButton" style="background-color: deepgreen" value="Retour">
            </form>
        </div>

    </div>

</body>
</html>