<?php
// Model: Course (Liste de courses)

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ===== CRUD Course =====

    // Générer une liste de courses (CREATE course + articles)
    public function genererListeCourses($id_utilisateur, $date, $statut, $articles = []) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO course (id_utilisateur, date, statut) VALUES (:id_utilisateur, :date, :statut)"
        );
        $stmt->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':date' => $date,
            ':statut' => $statut
        ]);
        $id_course = $this->pdo->lastInsertId();

        // Ajouter les articles si fournis
        foreach ($articles as $article) {
            $this->ajouterArticle($id_course, $article['id_aliment'], $article['quantite']);
        }

        return $id_course;
    }

    // Consulter toutes les courses
    public function consulterCourses() {
        $stmt = $this->pdo->query(
            "SELECT c.*, COUNT(ca.id_aliment) AS nb_articles,
                    SUM(ca.achete) AS nb_achetes
             FROM course c
             LEFT JOIN course_aliment ca ON c.id_course = ca.id_course
             GROUP BY c.id_course
             ORDER BY c.date DESC"
        );
        return $stmt->fetchAll();
    }

    // Lister tout (alias)
    public function listerTout() {
        return $this->consulterCourses();
    }

    // Obtenir une course par ID avec ses articles
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM course WHERE id_course = :id");
        $stmt->execute([':id' => $id]);
        $course = $stmt->fetch();

        if ($course) {
            $course['articles'] = $this->getArticles($id);
        }

        return $course;
    }

    // Modifier une course
    public function modifier($id, $id_utilisateur, $date, $statut) {
        $stmt = $this->pdo->prepare(
            "UPDATE course SET id_utilisateur = :id_utilisateur, date = :date, statut = :statut 
             WHERE id_course = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':id_utilisateur' => $id_utilisateur,
            ':date' => $date,
            ':statut' => $statut
        ]);
    }

    // Supprimer une course (cascade supprime les articles)
    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM course WHERE id_course = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ===== Gestion des articles (course_aliment) =====

    // Récupérer les articles d'une course avec infos aliment
    public function getArticles($id_course) {
        $stmt = $this->pdo->prepare(
            "SELECT ca.*, a.nom, a.categorie, a.kcal_portion, a.co2_impact, a.est_bio, a.est_local
             FROM course_aliment ca
             INNER JOIN aliment a ON ca.id_aliment = a.id_aliment
             WHERE ca.id_course = :id_course
             ORDER BY a.categorie, a.nom"
        );
        $stmt->execute([':id_course' => $id_course]);
        return $stmt->fetchAll();
    }

    // Ajouter un article à une course
    public function ajouterArticle($id_course, $id_aliment, $quantite) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO course_aliment (id_course, id_aliment, quantite, achete) 
             VALUES (:id_course, :id_aliment, :quantite, 0)"
        );
        return $stmt->execute([
            ':id_course' => $id_course,
            ':id_aliment' => $id_aliment,
            ':quantite' => $quantite
        ]);
    }

    // Supprimer un article d'une course
    public function supprimerArticle($id_course, $id_aliment) {
        $stmt = $this->pdo->prepare(
            "DELETE FROM course_aliment WHERE id_course = :id_course AND id_aliment = :id_aliment"
        );
        return $stmt->execute([
            ':id_course' => $id_course,
            ':id_aliment' => $id_aliment
        ]);
    }

    // Marquer un article comme acheté / non acheté
    public function marquerAchete($id_course, $id_aliment) {
        $stmt = $this->pdo->prepare(
            "UPDATE course_aliment SET achete = NOT achete 
             WHERE id_course = :id_course AND id_aliment = :id_aliment"
        );
        return $stmt->execute([
            ':id_course' => $id_course,
            ':id_aliment' => $id_aliment
        ]);
    }

}
