<?php
$userLoggedIn ??= '';
$erreur       ??= false;
$title = 'Problème de taille';
$css   = 'EnigmeTaille.css';
require __DIR__ . '/../layout/header.php';
?>

<div id="main">
    <div id="text">
        Quel attribut pourrait changer la hauteur de ce pied ? <br> Il faudrait qu'il ne mesure que 20cm!
    </div>
</div>

<form id="rep" method="POST" action="EnigmeTaille.php">
    <?php if ($erreur): ?>
        <p id="faux">Non :)</p>
    <?php endif; ?>
    <input type="checkbox" name="rep1"><label>width: 20px;</label><br>
    <input type="checkbox" name="rep2"><label>width: 20cm;</label><br>
    <input type="checkbox" name="rep3"><label>height: 20px;</label><br>
    <input type="checkbox" name="rep4"><label>height: 20cm;</label><br>
    <input type="submit" name="Envoyer" value="Soumettre">
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>
