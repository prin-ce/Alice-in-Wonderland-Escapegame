<?php
$userLoggedIn ??= '';
$erreur       ??= false;
$title = 'Miam';
$css   = 'EnigmeQCM1.css';
require __DIR__ . '/../layout/header.php';
?>

<div id="main">
    <p>Comment faire pour <br> ingérer les deux<br>à la suite ?</p>
    <h4>(en PHP)</h4>
</div>

<form id="rep" method="post" action="EnigmeMiam.php">
    <?php if ($erreur): ?>
        <p id="faux">Non :)</p>
    <?php endif; ?>
    <input type="checkbox" name="rep[]" value="$Drink + $Eat"><label>"$Drink + $Eat"</label><br>
    <input type="checkbox" name="rep[]" value="$Drink . $Eat"><label>"$Drink . $Eat"</label><br>
    <input type="checkbox" name="rep[]" value="$Drink , $Eat"><label>"$Drink , $Eat"</label><br>
    <input type="checkbox" name="rep[]" value="$Drink = $Eat"><label>"$Drink = $Eat"</label><br>
    <input type="checkbox" name="rep[]" value="$Drink * $Eat"><label>"$Drink * $Eat"</label><br>
    <input type="submit" name="Send" value="Soumettre">
</form>

<script type="text/javascript" src="../js/lapin.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
