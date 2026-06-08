<?php
$userLoggedIn ??= '';
$erreur       ??= false;
$quasi        ??= false;
$rep          ??= '';
$title = 'Le Tableau';
$css   = 'EnigmeTableau.css';
require __DIR__ . '/../layout/header.php';
?>

<div id="main">
    <div id="text">
        <p>"cartes", quel beau tableau !</p>
        <p>En parlant de ça, comment pourrais-je selectionner la 2ème personne de ce tableau ?</p>
    </div>
</div>

<form id="rep" method="POST" action="EnigmeTableau.php">
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
