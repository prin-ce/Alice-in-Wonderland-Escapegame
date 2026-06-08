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
    $firstName = $_POST["firstName"];
    $lastName  = $_POST["lastName"];
    $email     = $_POST["email"];

    // Vérifier l'unicité de l'email (en excluant l'utilisateur courant)
    $stmt = mysqli_prepare($connexion,
        "SELECT username FROM users WHERE email = ? AND username != ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $userLoggedIn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_fetch_assoc($result)) {
        // Email déjà pris par un autre utilisateur
        $detailsMessage = "<div class='alertError'>Cet email est déjà utilisé par un autre compte.</div>";
    } else {
        // Mise à jour sécurisée
        $detailsMessage = "<div class='alertSuccess'>Profil mis à jour avec succès.</div>";

        $stmt = mysqli_prepare($connexion,
            "UPDATE users SET firstname = ?, lastname = ?, email = ? WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "ssss", $firstName, $lastName, $email, $userLoggedIn);
        mysqli_stmt_execute($stmt);
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