<?php
// data/progression.php
// À inclure dans chaque page d'énigme pour contrôler l'accès et gérer la progression
require_once(__DIR__ . '/base.php');

// 1. Vérification de l'utilisateur connecté
if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../register");
    exit;
}

// 2. Vérification de l'existence d'une partie en cours dans la session
if (!isset($_SESSION['partie_id'])) {
    header("Location: ../browse");
    exit;
}

$partie_id = (int) $_SESSION['partie_id'];

// 3. Vérification que la partie est toujours "en_cours" en base
//    On récupère aussi l'énigme actuelle pour éviter un second SELECT plus tard
$stmt = mysqli_prepare($connexion,
    "SELECT enigme_courante FROM progression WHERE id = ? AND statut = 'en_cours'");
mysqli_stmt_bind_param($stmt, "i", $partie_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    // Partie inexistante ou déjà terminée : nettoyage et redirection
    unset($_SESSION['partie_id'], $_SESSION['enigme_courante']);
    header("Location: ../browse");
    exit;
}

// Énigme courante récupérée, disponible pour la suite
$enigme_courante_actuelle = (int) $row['enigme_courante'];

/**
 * Incrémente l'énigme courante et marque la partie comme terminée si le maximum est atteint.
 *
 * @param mysqli $connexion Connexion à la base de données
 * @param int    $partie_id Identifiant de la partie en cours
 * @param int    $enigme_courante_actuelle L'énigme actuelle (déjà validée)
 * @param int    $enigme_max Nombre total d'énigmes (défaut 8)
 * @return bool True si la partie est maintenant terminée, false sinon
 */
function avancer_enigme($connexion, $partie_id, $enigme_courante_actuelle, $enigme_max = 8) {
    $nouvelle_enigme = $enigme_courante_actuelle + 1;

    // Mise à jour de la session
    $_SESSION['enigme_courante'] = $nouvelle_enigme;

    // Si on atteint ou dépasse le maximum, la partie est terminée
    if ($nouvelle_enigme >= $enigme_max) {
        // Clôture de la partie + calcul du score (secondes écoulées depuis date_debut)
        $stmt = mysqli_prepare($connexion,
            "UPDATE progression
             SET enigme_courante = ?,
                 statut          = 'termine',
                 date_fin        = NOW(),
                 score           = TIMESTAMPDIFF(SECOND, date_debut, NOW())
             WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $enigme_max, $partie_id);
        mysqli_stmt_execute($stmt);

        // Conserve l'id pour que Fin.php puisse afficher le score
        $_SESSION['last_partie_id'] = $partie_id;
        unset($_SESSION['partie_id'], $_SESSION['enigme_courante']);
        return true;
    }

    // Sinon, on avance simplement
    $stmt = mysqli_prepare($connexion,
        "UPDATE progression SET enigme_courante = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $nouvelle_enigme, $partie_id);
    mysqli_stmt_execute($stmt);
    return false;
}