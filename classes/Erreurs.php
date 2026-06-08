<?php
class Erreurs {

	public static $firstNameCharacters = "Le prénom doit avoir entre 2 et 25 caractères";
    public static $lastNameCharacters = "Le nom doit avoir entre 2 et 25 caractères";
    public static $usernameCharacters = "Le pseudo doit avoir entre 5 et 25 caractères (lettres, chiffres, - et _ uniquement)";
    public static $usernameTaken = "Ce nom d'utilisateur est déjà pris";
    public static $emailsDoNotMatch = "Les adresse mails doivent-être identiques";
    public static $emailInvalid = "L’adresse email doit contenir un @";
    public static $emailTaken = "Cette adresse mail est déjà utilisée pour un compte";
    public static $passwordsDoNoMatch = "Le mot de passe et sa confirmation doivent-être identiques.";
    public static $passwordCharacters = "Le mot de passe doit avoir entre 5 et 30 caractères";
	public static $passwordNotAlphanumeric = "Le mot de passe ne doit contenir que des chiffres et des lettres";

    public static $loginFailed = "Votre pseudo ou mot de passe est incorrect";

}
?>