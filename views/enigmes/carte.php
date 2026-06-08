<?php
$userLoggedIn ??= '';
$erreur       ??= false;
$quasi        ??= false;
$rep          ??= '';
$title = 'La Carte';
$css   = 'EnigmeCarte.css';
require __DIR__ . '/../layout/header.php';
?>

<div id="main">
    <p>Alice aimerait choisir une des 52 cartes aléatoirement, avec quelle fonction php pourrait-elle procéder ?</p>
    <p>(précisez les parametres de cette fonction)</p>
</div>

<form id="rep" method="POST" action="EnigmeCarte.php">
    <?php if ($quasi): ?>
        <p id="quasi">Presque ! Veuillez préciser les parametres.</p>
    <?php elseif ($erreur): ?>
        <p id="faux">Non :)</p>
    <?php endif; ?>
    <input type="text" name="reponse" placeholder="Saisir votre réponse ici"
           value="<?= htmlspecialchars($rep, ENT_QUOTES, 'UTF-8') ?>">
    <input type="submit" name="Envoyer" value="Soumettre">
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>
