<?php
// Model: CategorieAlimentaire

class CategorieAlimentaire {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // CREATE
    public function ajouter($nom, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO categorie_alimentaire (nom, description) VALUES (:nom, :description)");
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description
        ]);
        return $this->pdo->lastInsertId();
    }

    // READ ALL
    public function listerTout() {
        $stmt = $this->pdo->query("SELECT c.*, COUNT(a.id_aliment) AS nb_aliments 
                                   FROM categorie_alimentaire c 
                                   LEFT JOIN aliment a ON c.id_categorie = a.id_categorie 
                                   GROUP BY c.id_categorie 
                                   ORDER BY c.nom");
        return $stmt->fetchAll();
    }

    // READ ONE
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie_alimentaire WHERE id_categorie = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // UPDATE
    public function modifier($id, $nom, $description) {
        $stmt = $this->pdo->prepare("UPDATE categorie_alimentaire SET nom = :nom, description = :description WHERE id_categorie = :id");
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description
        ]);
    }

    // DELETE
    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categorie_alimentaire WHERE id_categorie = :id");
        return $stmt->execute([':id' => $id]);
    }
}
