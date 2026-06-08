<?php
	// Inclusion des fichiers
	require_once(__DIR__ . "/../data/base.php");
	require_once(__DIR__ . "/../classes/Fonctions.php");
	require_once(__DIR__ . "/../classes/Erreurs.php");

	$account = new Fonctions($connexion);

	// Génération du token CSRF (valable pour toute la session)
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}

	// Validation CSRF sur tout POST avant tout traitement
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (
			!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
			!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
		) {
			header("Location: register");
			exit;
		}
	}

	require_once(__DIR__ . "/../data/inscription.php");
	require_once(__DIR__ . "/../data/connexion.php");

	function getInputValue($name) {
		if(isset($_POST[$name])) {
			echo $_POST[$name];
		}
	}
?>


<html>
<head>
	<title>Entre dans la légende</title>
	<link rel="stylesheet" type="text/css" href="css/register_style.css">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"
	        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
	        crossorigin="anonymous"></script>
	<script src="js/register.js"></script>
</head>
<body>

    <?php  

    if(isset($_POST['register_button'])) {
        echo '
        <script>

        $(document).ready(function() {
            $("#first").hide();
            $("#second").show();
        });

        </script>

        ';
    }

    ?>

	<div class="wrapper">

		<div class="login_box">

			<div class="login_header">
				<h1>Bienvenue!</h1>
				Connecte ou inscris toi en dessous!
			</div>
			<br>
			<div id="first">

                <form id="loginForm" action="register.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <br>
                    <?php echo $account->getError(Erreurs::$loginFailed); ?>
                    <input id="loginUsername" name="loginUsername" type="text" placeholder="Pseudo" value="<?php getInputValue('loginUsername') ?>" required>
					
					<br>
                    <input id="loginPassword" name="loginPassword" type="password" placeholder="Mot de passe" required>
					
                    <br>
					<input type="submit" name="login_button" value="Connexion">

                    <br>
					<div class="hasAccountText">
						<a href="#" id="signup" class="signup">Pas encore de compte? L'inscription c'est par ici.</a>
					</div>

				</form>

			</div>

			<div id="second">

            <form id="registerForm" action="register.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
					<br>
                    <?php echo $account->getError(Erreurs::$usernameCharacters); ?>
                    <?php echo $account->getError(Erreurs::$usernameTaken); ?>
                    <input id="username" name="username" type="text" placeholder="Pseudo" value="<?php getInputValue('username') ?>" required>
					
					<br>
                    <?php echo $account->getError(Erreurs::$firstNameCharacters); ?>
                    <input id="firstName" name="firstName" type="text" placeholder="Prénom" value="<?php getInputValue('firstName') ?>" required>
					
					<br>
                    <?php echo $account->getError(Erreurs::$lastNameCharacters); ?>
                    <input id="lastName" name="lastName" type="text" placeholder="Nom" value="<?php getInputValue('lastName') ?>" required>					

					<br>
                    <?php echo $account->getError(Erreurs::$emailsDoNotMatch); ?>
                    <?php echo $account->getError(Erreurs::$emailInvalid); ?>
                    <?php echo $account->getError(Erreurs::$emailTaken); ?>
                    <input id="email" name="email" type="email" placeholder="Email" value="<?php getInputValue('email') ?>" required>
					
					<br>
                    <input id="email2" name="email2" type="email" placeholder="Confirmation d'email" value="<?php getInputValue('email2') ?>" required>					

					<br>
                    <?php echo $account->getError(Erreurs::$passwordsDoNoMatch); ?>
                    <?php echo $account->getError(Erreurs::$passwordNotAlphanumeric); ?>
                    <?php echo $account->getError(Erreurs::$passwordCharacters); ?>
                    <input id="password" name="password" type="password" placeholder="Mot de passe" required>					

					<br>
                    <input id="password2" name="password2" type="password" placeholder="Confirmation de mot passe" required>
					
                    <br>
					<input type="submit" name="register_button" value="Inscription">

                    <br>
					<div class="hasAccountText">
						<a href="#" id="signin" class="signin">Déjà membre? Connecte-toi.</a>
					</div>
					
				</form>
			</div>

		</div>

	</div>


</body>
</html>