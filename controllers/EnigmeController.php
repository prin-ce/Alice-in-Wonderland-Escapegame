<?php

// Fonction libre : require dans une méthode de classe ne partage pas ses variables locales
// avec le fichier inclus. Une fonction libre le fait correctement via ses paramètres.
function enigme_render(
    string $path,
    string $userLoggedIn,
    bool   $erreur = false,
    bool   $quasi  = false,
    string $rep    = ""
): void {
    require $path;
}

class EnigmeController {

    private mysqli $db;
    private string $userLoggedIn;
    private int    $partie_id;
    private int    $enigme_courante;

    public function __construct(mysqli $db, int $enigme_courante, array $session) {
        $this->db              = $db;
        $this->enigme_courante = $enigme_courante;
        $this->userLoggedIn    = $session['userLoggedIn'];
        $this->partie_id       = (int) $session['partie_id'];
    }

    // Appelle avancer_enigme et redirige vers la page suivante ou Fin.php
    private function avancer(string $next): void {
        $termine = avancer_enigme($this->db, $this->partie_id, $this->enigme_courante);
        header("Location: " . ($termine ? "Fin" : $next));
        exit;
    }

    // Charge la vue correspondante en lui transmettant les variables nécessaires
    private function render(string $vue, array $donnees = []): void {
        enigme_render(
            __DIR__ . "/../views/enigmes/{$vue}.php",
            $this->userLoggedIn,
            $donnees['erreur'] ?? false,
            $donnees['quasi']  ?? false,
            $donnees['rep']    ?? ""
        );
    }

    // -----------------------------------------------------------------
    // Enigmes
    // -----------------------------------------------------------------

    public function miam(): void {
        $erreur = false;
        if (isset($_POST['Send'])) {
            $rep = $_POST['rep'] ?? [];
            if (count($rep) === 1 && $rep[0] === '$Drink . $Eat') {
                $this->avancer('../transitions/tableau');
            }
            $erreur = true;
        }
        $this->render('miam', compact('erreur'));
    }

    public function taille(): void {
        $erreur = false;
        if (isset($_POST['Envoyer'])) {
            if (!isset($_POST['rep1']) && !isset($_POST['rep2']) && !isset($_POST['rep3']) && isset($_POST['rep4'])) {
                $this->avancer('../transitions/miam');
            }
            $erreur = true;
        }
        $this->render('taille', compact('erreur'));
    }

    public function carte(): void {
        $rep   = "";
        $erreur = false;
        $quasi  = false;
        if (isset($_POST['Envoyer'])) {
            $rep        = $_POST['reponse'] ?? "";
            $normalized = str_replace(' ', '', $rep);
            if ($normalized === "rand(1,52)") {
                $this->avancer('../transitions/snake');
            }
            $erreur = true;
            $quasi  = ($normalized === "rand()");
        }
        $this->render('carte', compact('rep', 'erreur', 'quasi'));
    }

    public function lapin(): void {
        $rep   = "";
        $erreur = false;
        if (isset($_POST['Envoyer'])) {
            $rep = $_POST['reponse'] ?? "";
            if (strtoupper(trim($rep)) === "TEMPS") {
                $this->avancer('Fin.php');
            }
            $erreur = true;
        }
        $this->render('lapin', compact('rep', 'erreur'));
    }

    public function tableau(): void {
        $rep   = "";
        $erreur = false;
        $quasi  = false;
        if (isset($_POST['Envoyer'])) {
            $rep        = $_POST['reponse'] ?? "";
            $normalized = str_replace(' ', '', ltrim($rep, '$'));
            if ($normalized === "cartes[1]") {
                $this->avancer('../transitions/carte');
            }
            $erreur = true;
            $quasi  = ($normalized === "cartes[]");
        }
        $this->render('tableau', compact('rep', 'erreur', 'quasi'));
    }

    public function snake(): void {
        if (isset($_POST['snake_win'])) {
            if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header("Location: snake");
                exit;
            }
            $this->avancer('../transitions/fillette');
        }
        $this->render('snake');
    }

    public function fillette(): void {
        $erreur = false;
        if (isset($_POST['Envoyer'])) {
            if (isset($_POST['rep1']) && isset($_POST['rep3']) && !isset($_POST['rep2']) && !isset($_POST['rep4'])) {
                $this->avancer('../transitions/lapin');
            }
            $erreur = true;
        }
        $this->render('fillette', compact('erreur'));
    }
}
