<?php
// FrontOffice Model: Aliment (lecture seule — surface API restreinte)

class Aliment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listerTout() {
        $stmt = $this->pdo->query("SELECT * FROM aliment ORDER BY nom");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM aliment WHERE id_aliment = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function rechercher($q = null, $categorie = null, $bio = null, $local = null) {
        $sql = "SELECT * FROM aliment WHERE 1=1";
        $params = [];

        if ($q !== null && $q !== '') {
            $sql .= " AND nom LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }
        if ($categorie !== null && $categorie !== '') {
            $sql .= " AND categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        if ($bio === '1' || $bio === '0') {
            $sql .= " AND est_bio = :bio";
            $params[':bio'] = (int)$bio;
        }
        if ($local === '1' || $local === '0') {
            $sql .= " AND est_local = :local";
            $params[':local'] = (int)$local;
        }

        $sql .= " ORDER BY nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCategories() {
        $stmt = $this->pdo->query(
            "SELECT DISTINCT categorie FROM aliment ORDER BY categorie"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function statistiques() {
        $stats = [
            'total' => 0, 'nb_bio' => 0, 'nb_local' => 0,
            'kcal_moyen' => 0, 'co2_moyen' => 0, 'par_categorie' => []
        ];

        $row = $this->pdo->query(
            "SELECT COUNT(*) AS total,
                    SUM(est_bio) AS nb_bio,
                    SUM(est_local) AS nb_local,
                    AVG(kcal_portion) AS kcal_moyen,
                    AVG(co2_impact) AS co2_moyen
             FROM aliment"
        )->fetch();

        if ($row) {
            $stats['total']      = (int)$row['total'];
            $stats['nb_bio']     = (int)$row['nb_bio'];
            $stats['nb_local']   = (int)$row['nb_local'];
            $stats['kcal_moyen'] = round((float)$row['kcal_moyen'], 1);
            $stats['co2_moyen']  = round((float)$row['co2_moyen'], 2);
        }

        $stmt = $this->pdo->query(
            "SELECT categorie, COUNT(*) AS nb FROM aliment GROUP BY categorie ORDER BY nb DESC"
        );
        $stats['par_categorie'] = $stmt->fetchAll();

        return $stats;
    }
}
