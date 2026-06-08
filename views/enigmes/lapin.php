<?php
$userLoggedIn ??= '';
$erreur       ??= false;
$rep          ??= '';
$title = 'Le Lapin';
$css   = 'EnigmeLapin.css';
require __DIR__ . '/../layout/header.php';
?>

<div id="main">
    <div id="enm">
        <p id="a">...</p>
        <p id="b">J'ai pas le temps. Demandez à votre ami l'ordinateur.</p>
    </div>
    <div id="text">
        <p>On dirait qu'il veut nous dire quelque chose...</p>
        <p>Trouvez comment le faire parler</p>
    </div>
</div>

<form id="rep" method="POST" action="EnigmeLapin.php">
    <?php if ($erreur): ?>
        <p id="faux">Non :)</p>
    <?php endif; ?>
    <input type="text" name="reponse" placeholder="Saisir votre réponse ici"
           value="<?= htmlspecialchars($rep, ENT_QUOTES, 'UTF-8') ?>">
    <input type="submit" name="Envoyer" value="Soumettre">
</form>

<script type="text/javascript" src="../js/lapin.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
