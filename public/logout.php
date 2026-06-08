<?php
session_start();

// 1. Vider le tableau de session (données inaccessibles dès maintenant)
$_SESSION = [];

// 2. Supprimer le cookie de session côté navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Détruire les données de session côté serveur
session_destroy();

header("Location: register");
exit;
