<?php
require_once __DIR__ . '/base.php';

if(isset($_POST['login_button'])) {
	//Login button was pressed
	$username = $_POST['loginUsername'];
	$password = $_POST['loginPassword'];

	$result = $account->login($username, $password);

    //si une session est déjà "isset" avec ce visiteur, on l'informe:
    if(isset($_SESSION['loginUsername'])){
        echo "Vous êtes déjà connecté, veuillez accéder directement à l'espace membre.";
    }

	if($result == true) {
		// Récupérer l'ID pour l'utiliser dans toute l'application
		$id_stmt = mysqli_prepare($connexion, "SELECT id FROM users WHERE username = ?");
		mysqli_stmt_bind_param($id_stmt, "s", $username);
		mysqli_stmt_execute($id_stmt);
		$id_row = mysqli_fetch_assoc(mysqli_stmt_get_result($id_stmt));

		// Invalide l'ID de session pré-login pour prévenir la fixation de session
		session_regenerate_id(true);

		$_SESSION['user_id']      = $id_row ? (int) $id_row['id'] : null;
		$_SESSION['userLoggedIn'] = $username;
		header("Location: browse");
		exit;
	}

}
?>