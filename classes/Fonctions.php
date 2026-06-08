<?php
	class Fonctions {

	    // Les propriétés sont définies en mode privé
		private $connexion;
		private $errorArray;

		public function __construct($connexion) 
		{
			$this->connexion = $connexion;
			$this->errorArray = array();
		}

        // on récupère le pseudo
		public function login($username, $mdp) 
		{

			// On prépare la requête avec un placeholder "?"
			// PHP envoie la valeur séparément : le moteur SQL ne peut plus
			// l'interpréter comme du code, seulement comme une donnée
			$stmt = mysqli_prepare($this->connexion, "SELECT password FROM users WHERE username=?");

			// On lie la variable $username au placeholder
			// "s" signifie "string" : type de la variable
			mysqli_stmt_bind_param($stmt, "s", $username);

			// On exécute
			mysqli_stmt_execute($stmt);

			// On récupère le résultat
			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_assoc($result);

			// Si l'username existe ET que le mot de passe correspond au hash BCrypt stocké
			if ($row && password_verify($mdp, $row['password'])) {
				return true;
			}

			array_push($this->errorArray, Erreurs::$loginFailed);
			return false;
		}

		public function register($username, $firstname, $lastname, $email, $email2, $mdp, $mdp2) 
		{
			$this->validateUsername($username);
			$this->validateFirstName($firstname);
			$this->validateLastName($lastname);
			$this->validateEmails($email, $email2);
			$this->validatePasswords($mdp, $mdp2);

			if(empty($this->errorArray) == true) {
				// on insère les données à partir de la fonction insertUserDetails
				return $this->insertUserDetails($username, $firstname, $lastname, $email, $mdp);
			}
			else {
				return false;
			}

		}

		public function getError($error) 
		{
			if(!in_array($error, $this->errorArray)) {
				$error = "";
			}
			return "<span class='errorMessage'>$error</span>";
		}

		// On insère les données dans la base
		private function insertUserDetails($username, $firstname, $lastname, $email, $mdp) 
		{
			// Utilisation de password_hash pour un hachage sécurisé des mots de passe
			$encryptedPw = password_hash($mdp, PASSWORD_DEFAULT);
		
			// Génération d'une photo de profil aléatoire
			$profilePic = "../images/avatars/defaults/default" . rand(1, 8) . ".png";
		
			// Récupération de la date actuelle
			$date = date("Y-m-d");
		
			// Préparation de la requête SQL
			$query = "INSERT INTO users (username, firstName, lastName, email, password, signUpDate, profilePic) 
				VALUES (?, ?, ?, ?, ?, ?, ?)";
		
			// Utilisation de requêtes préparées pour éviter les injections SQL
			$stmt = $this->connexion->prepare($query);
			$stmt->bind_param("sssssss", $username, $firstname, $lastname, $email, $encryptedPw, $date, $profilePic);			
		
			// Exécution de la requête
			$result = $stmt->execute();

			// Vérification des erreurs SQL
			if (!$result) {
				die("Erreur SQL : " . $stmt->error);
			}
		
			// Fermeture du statement
			$stmt->close();
		
			return $result;
		}
		

		// vérification du pseudo
		private function validateUsername($username)
		{
			// Format avant d'interroger la base : lettres, chiffres, - et _, 5-25 caractères
			if (!preg_match('/^[A-Za-z0-9_-]{5,25}$/', $username)) {
				array_push($this->errorArray, Erreurs::$usernameCharacters);
				return false;
			}

			$stmt = mysqli_prepare($this->connexion, "SELECT username FROM users WHERE username=?");

			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);

			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_assoc($result);

			if ($row) {
				array_push($this->errorArray, Erreurs::$usernameTaken);
				return false;
			}

			return true;
		}

        // vérification du prénom
		private function validateFirstName($firstname) 
		{
            // un prénom devra avoir entre 2 et 25 lettres
			if(strlen($firstname) > 25 || strlen($firstname) < 2) {
				array_push($this->errorArray, Erreurs::$firstNameCharacters);
				return;
			}
		}

        // vérification du nom
		private function validateLastName($lastname) 
		{
			if(strlen($lastname) > 25 || strlen($lastname) < 2) {
				array_push($this->errorArray, Erreurs::$lastNameCharacters);
				return;
			}
		}

        // vérification de l'email
		private function validateEmails($email, $email2) 
		{
			// 1. Vérifier que les deux emails correspondent
			if ($email != $email2) {
				array_push($this->errorArray, Erreurs::$emailsDoNotMatch);
				return false;
			}

			// 2. Vérifier le format avec filter_var
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				array_push($this->errorArray, Erreurs::$emailInvalid);
				return false;
			}

			// 3. Vérifier l'unicité en base (requête préparée)
			$stmt = mysqli_prepare($this->connexion, "SELECT email FROM users WHERE email=?");

			mysqli_stmt_bind_param($stmt, "s", $email);
			mysqli_stmt_execute($stmt);

			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_assoc($result);

			if ($row) {
				array_push($this->errorArray, Erreurs::$emailTaken);
				return false;
			}

			return true;
		}

        // vérification du mot de passe
		private function validatePasswords($mdp, $mdp2) {
			
			if($mdp != $mdp2) {
				array_push($this->errorArray, Erreurs::$passwordsDoNoMatch);
				return;
			}

            // la preg_match sert à définir le masque
            // le masque défini A-Z pour toutes les lettres enmajuscule, a-z pour
            // les minuscules et 0-9 pour les chiffres;
			if(preg_match('/[^A-Za-z0-9]/', $mdp)) {
				array_push($this->errorArray, Erreurs::$passwordNotAlphanumeric);
				return;
			}

			if(strlen($mdp) > 30 || strlen($mdp) < 5) {
				array_push($this->errorArray, Erreurs::$passwordCharacters);
				return;
			}

		}

	}
?>