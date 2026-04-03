<?php
class ProfilSportif {
    private $id;
    private $utilisateur_id;
    private $sport;
    private $objectif;
    private $niveau;
    private $frequence;

    public function __construct($id = null, $utilisateur_id = null, $sport = '', $objectif = '', $niveau = '', $frequence = 0) {
        $this->id = $id;
        $this->utilisateur_id = $utilisateur_id;
        $this->sport = $sport;
        $this->objectif = $objectif;
        $this->niveau = $niveau;
        $this->frequence = $frequence;
    }

    public function getId() { return $this->id; }
    public function getUtilisateurId() { return $this->utilisateur_id; }
    public function getSport() { return $this->sport; }
    public function getObjectif() { return $this->objectif; }
    public function getNiveau() { return $this->niveau; }
    public function getFrequence() { return $this->frequence; }

    public function setSport($sport) { $this->sport = $sport; }
    public function setObjectif($objectif) { $this->objectif = $objectif; }
    public function setNiveau($niveau) { $this->niveau = $niveau; }
    public function setFrequence($frequence) { $this->frequence = $frequence; }
}
?>
