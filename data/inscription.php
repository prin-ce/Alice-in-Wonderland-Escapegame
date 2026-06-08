<?php
require_once __DIR__ . '/base.php';

function sanitizeFormPassword($inputText) {
	$inputText = strip_tags($inputText);
	return $inputText;
}

function sanitizeFormUsername($inputText) {
	$inputText = strip_tags($inputText);
	$inputText = str_replace(" ", "", $inputText);
	return $inputText;
}

function sanitizeFormString($inputText) {
	$inputText = strip_tags($inputText);
	$inputText = str_replace(" ", "", $inputText);
	$inputText = ucfirst(strtolower($inputText));
	return $inputText;
}

// Normalisation dédiée aux emails : trim + lowercase uniquement.
// Pas de ucfirst (corrompt l'adresse), pas de str_replace (les espaces
// internes doivent être rejetés par filter_var, pas silencieusement supprimés).
function sanitizeEmail(string $inputText): string {
	return strtolower(trim(strip_tags($inputText)));
}


if(isset($_POST['register_button'])) {
	//Register button was pressed
	$username = sanitizeFormUsername($_POST['username']);
	$firstName = sanitizeFormString($_POST['firstName']);
	$lastName = sanitizeFormString($_POST['lastName']);
	$email = sanitizeEmail($_POST['email']);
	$email2 = sanitizeEmail($_POST['email2']);
	$password = sanitizeFormPassword($_POST['password']);
	$password2 = sanitizeFormPassword($_POST['password2']);

	$wasSuccessful = $account->register($username, $firstName, $lastName, $email, $email2, $password, $password2);

	if($wasSuccessful == true) {
		header("Location: register");
		exit;
	}

}


?>