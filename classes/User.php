<?php
class User {

    private mysqli $connexion;
    private string $username;
    private ?array $profile = null;

    public function __construct(mysqli $connexion, string $username) {
        $this->connexion = $connexion;
        $this->username  = $username;
    }

    // Une seule requête préparée, résultat mis en cache pour tous les getters
    private function load(): void {
        if ($this->profile !== null) return;

        $stmt = mysqli_prepare($this->connexion,
            "SELECT username, firstName, lastName, email FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $this->username);
        mysqli_stmt_execute($stmt);
        $this->profile = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
    }

    public function getUsername(): string  { $this->load(); return $this->profile['username']   ?? ''; }
    public function getFirstName(): string { $this->load(); return $this->profile['firstName']  ?? ''; }
    public function getLastName(): string  { $this->load(); return $this->profile['lastName']   ?? ''; }
    public function getEmail(): string     { $this->load(); return $this->profile['email']      ?? ''; }
}
