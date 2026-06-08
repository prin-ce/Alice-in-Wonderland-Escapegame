<?php
$title        ??= '';
$css          ??= 'style.css';
$userLoggedIn ??= '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>

<div id="HeadBan">
    <p id="NomJeu">Escape Game : Alice au pays des merveilles</p>
    <span id="playerName"><?php echo htmlspecialchars($userLoggedIn, ENT_QUOTES, 'UTF-8'); ?></span>
    <div id="Player">
        <a href="../profil"><img id="ppPlayer" src="../images/icons/pp.png"></a>
    </div>
</div><br>
