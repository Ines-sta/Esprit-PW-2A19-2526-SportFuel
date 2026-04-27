<?php
// FrontOffice Model: Course (lecture seule + toggle achat)
// Pas de méthodes ajouter/modifier/supprimer — réservées au BackOffice.

class Course {
    private $pdo;

    public static $unitesAutorisees = ['g', 'kg', 'ml', 'L', 'piece'];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

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

    public function listerTout() {
        return $this->consulterCourses();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM course WHERE id_course = :id");
        $stmt->execute([':id' => $id]);
        $course = $stmt->fetch();
        if ($course) {
            $course['articles'] = $this->getArticles($id);
        }
        return $course;
    }

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

    public function rechercher($q = null, $statut = null, $id_utilisateur = null) {
        $sql = "SELECT c.*, COUNT(ca.id_aliment) AS nb_articles,
                       SUM(ca.achete) AS nb_achetes
                FROM course c
                LEFT JOIN course_aliment ca ON c.id_course = ca.id_course
                WHERE 1=1";
        $params = [];

        if ($q !== null && $q !== '') {
            $sql .= " AND c.nom LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }
        if ($statut !== null && $statut !== '') {
            $sql .= " AND c.statut = :statut";
            $params[':statut'] = $statut;
        }
        if ($id_utilisateur !== null && $id_utilisateur !== '' && (int)$id_utilisateur > 0) {
            $sql .= " AND c.id_utilisateur = :id_utilisateur";
            $params[':id_utilisateur'] = (int)$id_utilisateur;
        }

        $sql .= " GROUP BY c.id_course ORDER BY c.date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ===== Helpers unités / kcal (mêmes règles qu'en BackOffice) =====

    public static function toGrams($quantite, $unite) {
        switch ($unite) {
            case 'g':     return (float)$quantite;
            case 'kg':    return (float)$quantite * 1000;
            case 'ml':    return (float)$quantite;
            case 'L':     return (float)$quantite * 1000;
            case 'piece': return null;
            default:      return (float)$quantite;
        }
    }

    public static function kcalArticle($article) {
        $grams = self::toGrams($article['quantite'], $article['unite'] ?? 'g');
        if ($grams === null) return null;
        return ($grams / 100.0) * (float)$article['kcal_portion'];
    }

    public static function kcalTotal($articles) {
        $total = 0;
        foreach ($articles as $art) {
            $k = self::kcalArticle($art);
            if ($k !== null) $total += $k;
        }
        return $total;
    }

    public function statistiques($id_utilisateur = null) {
        $stats = [
            'total' => 0, 'par_statut' => [], 'articles_moyen' => 0,
            'pourcent_achetes' => 0, 'total_kcal_global' => 0,
        ];

        $whereCourse = "";
        $params = [];
        if ($id_utilisateur !== null && $id_utilisateur !== '' && (int)$id_utilisateur > 0) {
            $whereCourse = " WHERE c.id_utilisateur = :id_utilisateur";
            $params[':id_utilisateur'] = (int)$id_utilisateur;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM course c" . $whereCourse);
        $stmt->execute($params);
        $row = $stmt->fetch();
        $stats['total'] = (int)($row['total'] ?? 0);

        $stmt = $this->pdo->prepare(
            "SELECT c.statut, COUNT(*) AS nb
             FROM course c"
             . $whereCourse .
            " GROUP BY c.statut"
        );
        $stmt->execute($params);
        $stats['par_statut'] = $stmt->fetchAll();

        $stmt = $this->pdo->prepare(
            "SELECT AVG(t.nb) AS articles_moyen FROM (
                SELECT COUNT(ca.id_aliment) AS nb
                FROM course c
                LEFT JOIN course_aliment ca ON c.id_course = ca.id_course"
                . $whereCourse .
                " GROUP BY c.id_course
             ) AS t"
        );
        $stmt->execute($params);
        $row = $stmt->fetch();
        $stats['articles_moyen'] = round((float)($row['articles_moyen'] ?? 0), 1);

        $stmt = $this->pdo->prepare(
            "SELECT SUM(ca.achete) AS achetes, COUNT(*) AS total
             FROM course_aliment ca
             INNER JOIN course c ON c.id_course = ca.id_course"
             . $whereCourse
        );
        $stmt->execute($params);
        $row = $stmt->fetch();
        $totalArticles = (int)($row['total'] ?? 0);
        $stats['pourcent_achetes'] = $totalArticles > 0
            ? round(((int)$row['achetes'] / $totalArticles) * 100, 1)
            : 0;

        $stmt = $this->pdo->prepare(
            "SELECT ca.quantite, ca.unite, a.kcal_portion
             FROM course_aliment ca
             INNER JOIN aliment a ON ca.id_aliment = a.id_aliment
             INNER JOIN course c ON c.id_course = ca.id_course"
             . $whereCourse
        );
        $stmt->execute($params);
        $totalKcal = 0;
        foreach ($stmt->fetchAll() as $art) {
            $k = self::kcalArticle($art);
            if ($k !== null) $totalKcal += $k;
        }
        $stats['total_kcal_global'] = round($totalKcal, 0);

        return $stats;
    }
}
