<?php
$userLoggedIn ??= '';
$erreur       ??= false;
$title = 'La Fillette';
$css   = 'EnigmeQCM2.css';
require __DIR__ . '/../layout/header.php';
?>

<div id="main">
    <p>Cheshire garde <br>la sortie</p>
</div>

<form id="rep" method="post" action="EnigmeFillette.php">
    <?php if ($erreur): ?>
        <p id="faux">Non :)</p>
    <?php endif; ?>
    <input type="checkbox" name="rep1" value=""><label>#Fillette { visibility: hidden; }</label><br>
    <input type="checkbox" name="rep2" value=""><label>.Fillette { visibility: hidden; }</label><br>
    <input type="checkbox" name="rep3" value=""><label>#Fillette { opacity: 0; }</label><br>
    <input type="checkbox" name="rep4" value=""><label>.Fillette { opacity: 0; }</label><br>
    <input type="submit" name="Envoyer" value="Soumettre">
</form>

<script type="text/javascript" src="../js/lapin.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
