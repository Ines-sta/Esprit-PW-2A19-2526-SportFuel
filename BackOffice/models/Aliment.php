<?php
// Model: Aliment

class Aliment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // CREATE
    public function ajouter($nom, $id_categorie, $kcal_portion, $co2_impact, $est_bio, $est_local) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO aliment (nom, id_categorie, kcal_portion, co2_impact, est_bio, est_local) 
             VALUES (:nom, :id_categorie, :kcal_portion, :co2_impact, :est_bio, :est_local)"
        );
        $stmt->execute([
            ':nom' => $nom,
            ':id_categorie' => $id_categorie,
            ':kcal_portion' => $kcal_portion,
            ':co2_impact' => $co2_impact,
            ':est_bio' => $est_bio,
            ':est_local' => $est_local
        ]);
        return $this->pdo->lastInsertId();
    }

    // READ ALL avec jointure sur categorie_alimentaire
    public function listerTout() {
        $stmt = $this->pdo->query(
            "SELECT a.*, c.nom AS nom_categorie 
             FROM aliment a 
             INNER JOIN categorie_alimentaire c ON a.id_categorie = c.id_categorie 
             ORDER BY a.nom"
        );
        return $stmt->fetchAll();
    }

    // READ ONE
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, c.nom AS nom_categorie 
             FROM aliment a 
             INNER JOIN categorie_alimentaire c ON a.id_categorie = c.id_categorie 
             WHERE a.id_aliment = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // UPDATE
    public function modifier($id, $nom, $id_categorie, $kcal_portion, $co2_impact, $est_bio, $est_local) {
        $stmt = $this->pdo->prepare(
            "UPDATE aliment SET nom = :nom, id_categorie = :id_categorie, kcal_portion = :kcal_portion, 
             co2_impact = :co2_impact, est_bio = :est_bio, est_local = :est_local 
             WHERE id_aliment = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':id_categorie' => $id_categorie,
            ':kcal_portion' => $kcal_portion,
            ':co2_impact' => $co2_impact,
            ':est_bio' => $est_bio,
            ':est_local' => $est_local
        ]);
    }

    // DELETE
    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM aliment WHERE id_aliment = :id");
        return $stmt->execute([':id' => $id]);
    }

}
