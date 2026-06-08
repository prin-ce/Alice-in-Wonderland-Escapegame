<?php
require_once __DIR__ . '/../../data/base.php';
require_once __DIR__ . '/../../data/progression.php';
require_once __DIR__ . '/../../controllers/EnigmeController.php';
(new EnigmeController($connexion, $enigme_courante_actuelle, $_SESSION))->fillette();
