<?php
class Utilisateur {
    private $id;
    private $nom;
    private $email;
    private $password;
    private $age;
    private $poids;
    private $taille;

    public function __construct($id = null, $nom = '', $email = '', $password = '', $age = 0, $poids = 0, $taille = 0) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->age = $age;
        $this->poids = $poids;
        $this->taille = $taille;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getAge() { return $this->age; }
    public function getPoids() { return $this->poids; }
    public function getTaille() { return $this->taille; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = password_hash($password, PASSWORD_BCRYPT); }
    public function setAge($age) { $this->age = $age; }
    public function setPoids($poids) { $this->poids = $poids; }
    public function setTaille($taille) { $this->taille = $taille; }
}
?>
