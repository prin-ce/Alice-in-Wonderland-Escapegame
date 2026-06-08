<?php
// updateDetails.php – traitement des formulaires de mise à jour
// $userLoggedIn est déjà défini dans profil.php
require_once(__DIR__ . '/base.php');

// Vérification CSRF pour toutes les actions sensibles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['saveDetailsButton']) || isset($_POST['savePasswordButton']))) {
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $detailsMessage = "<div class='alertError'>Erreur de sécurité : veuillez réessayer.</div>";
        return;
    }
}

// ---------------------------
// 1. Mise à jour du profil
// ---------------------------
if (isset($_POST["saveDetailsButton"])) {
    $newUsername = trim($_POST["username"] ?? '');
    $firstName   = $_POST["firstName"] ?? '';
    $lastName    = $_POST["lastName"]  ?? '';
    $email       = $_POST["email"]     ?? '';

    if (!preg_match('/^[A-Za-z0-9_-]{5,25}$/', $newUsername)) {
        $detailsMessage = "<div class='alertError'>Le pseudo doit avoir entre 5 et 25 caractères (lettres, chiffres, - et _).</div>";
    } else {
        // Vérifier l'unicité du pseudo (en excluant l'utilisateur courant)
        $stmt = mysqli_prepare($connexion,
            "SELECT id FROM users WHERE username = ? AND username != ?");
        mysqli_stmt_bind_param($stmt, "ss", $newUsername, $userLoggedIn);
        mysqli_stmt_execute($stmt);
        $usernameTaken = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        // Vérifier l'unicité de l'email (en excluant l'utilisateur courant)
        $stmt2 = mysqli_prepare($connexion,
            "SELECT id FROM users WHERE email = ? AND username != ?");
        mysqli_stmt_bind_param($stmt2, "ss", $email, $userLoggedIn);
        mysqli_stmt_execute($stmt2);
        $emailTaken = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

        if ($usernameTaken) {
            $detailsMessage = "<div class='alertError'>Ce pseudo est déjà pris.</div>";
        } elseif ($emailTaken) {
            $detailsMessage = "<div class='alertError'>Cet email est déjà utilisé par un autre compte.</div>";
        } else {
            $stmt3 = mysqli_prepare($connexion,
                "UPDATE users SET username = ?, firstName = ?, lastName = ?, email = ? WHERE username = ?");
            mysqli_stmt_bind_param($stmt3, "sssss", $newUsername, $firstName, $lastName, $email, $userLoggedIn);
            mysqli_stmt_execute($stmt3);

            // Mettre à jour la session si le pseudo a changé
            if ($newUsername !== $userLoggedIn) {
                $_SESSION['userLoggedIn'] = $newUsername;
                $userLoggedIn = $newUsername;
            }

            $detailsMessage = "<div class='alertSuccess'>Profil mis à jour avec succès.</div>";
        }
    }
}

// ---------------------------
// 2. Changement de mot de passe
// ---------------------------
if (isset($_POST["savePasswordButton"])) {
    $oldPassword    = $_POST["oldPassword"];
    $newPassword    = $_POST["newPassword"];
    $newPassword2   = $_POST["newPassword2"];

    // Récupérer le hash actuel
    $stmt = mysqli_prepare($connexion,
        "SELECT password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $userLoggedIn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $db_password = $row["password"] ?? null;

    // Vérifier l'ancien mot de passe avec password_verify()
    if ($row && password_verify($oldPassword, $db_password)) {
        if ($newPassword === $newPassword2) {
            if (strlen($newPassword) <= 4) {
                $passwordMessage = "Le mot de passe doit avoir plus de 4 caractères.<br><br>";
            } else {
                // Hachage sécurisé
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($connexion,
                    "UPDATE users SET password = ? WHERE username = ?");
                mysqli_stmt_bind_param($stmt, "ss", $newHash, $userLoggedIn);
                mysqli_stmt_execute($stmt);

                $passwordMessage = "<div class='alertSuccess'>Mot de passe modifié avec succès.</div>";
            }
        } else {
            $passwordMessage = "Les mots de passe ne sont pas identiques.<br><br>";
        }
    } else {
        $passwordMessage = "L'ancien mot de passe est incorrect.<br><br>";
    }
}
?>