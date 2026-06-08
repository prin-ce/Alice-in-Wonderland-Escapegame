<?php
$userLoggedIn ??= '';
$title = 'Petits Jeux';
$css   = 'snake.css';
require __DIR__ . '/../layout/header.php';
?>

<h1>Echappez vous de la prison de la Reine</h1>

<div id="reponses">
    <span id="reponse1"></span>
    <span id="reponse2"></span>
    <span id="reponse3"></span>
</div>

<div id="plateau"></div>

<div id="boite_enigme">
    <span id="probleme"></span>
</div>

<form id="snake-win-form" method="POST" action="snake.php" style="display:none">
    <input type="hidden" name="snake_win" value="1">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
<script type="text/javascript" src="../js/snake.js"></script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
