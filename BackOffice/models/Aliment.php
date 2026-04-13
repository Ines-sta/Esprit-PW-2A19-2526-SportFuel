<?php
// Model: Aliment

class Aliment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // CREATE
    public function ajouter($nom, $categorie, $kcal_portion, $co2_impact, $est_bio, $est_local) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO aliment (nom, categorie, kcal_portion, co2_impact, est_bio, est_local) 
             VALUES (:nom, :categorie, :kcal_portion, :co2_impact, :est_bio, :est_local)"
        );
        $stmt->execute([
            ':nom' => $nom,
            ':categorie' => $categorie,
            ':kcal_portion' => $kcal_portion,
            ':co2_impact' => $co2_impact,
            ':est_bio' => $est_bio,
            ':est_local' => $est_local
        ]);
        return $this->pdo->lastInsertId();
    }

    // READ ALL
    public function listerTout() {
        $stmt = $this->pdo->query(
            "SELECT * FROM aliment ORDER BY nom"
        );
        return $stmt->fetchAll();
    }

    // READ ONE
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM aliment WHERE id_aliment = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // UPDATE
    public function modifier($id, $nom, $categorie, $kcal_portion, $co2_impact, $est_bio, $est_local) {
        $stmt = $this->pdo->prepare(
            "UPDATE aliment SET nom = :nom, categorie = :categorie, kcal_portion = :kcal_portion, 
             co2_impact = :co2_impact, est_bio = :est_bio, est_local = :est_local 
             WHERE id_aliment = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':categorie' => $categorie,
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
