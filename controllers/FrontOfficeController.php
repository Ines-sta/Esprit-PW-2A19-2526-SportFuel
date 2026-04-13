<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Publication.php';
require_once __DIR__ . '/../models/Commentaire.php';

class FrontOfficeController {
    private $userModel;
    private $publicationModel;
    private $commentaireModel;
    private $sportif_id = 3; // Simuler connecté

    public function __construct() {
        $this->userModel = new User();
        $this->publicationModel = new Publication();
        $this->commentaireModel = new Commentaire();
    }

    public function handlePost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        // Add publication
        if (isset($_POST['action']) && $_POST['action'] === 'add_pub') {
            $validation = $this->publicationModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: dashboard.php");
                exit;
            }
            $stmt = $this->publicationModel->getPdo()->prepare("INSERT INTO publication (id_user, text, date) VALUES (?, ?, NOW())");
            $stmt->execute([$this->sportif_id, $_POST['text']]);
            header("Location: dashboard.php");
            exit;
        }

        // Edit publication
        if (isset($_POST['action']) && $_POST['action'] === 'edit_pub') {
            $validation = $this->publicationModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: dashboard.php");
                exit;
            }
            $stmt = $this->publicationModel->getPdo()->prepare("UPDATE publication SET text = ? WHERE id_pub = ? AND id_user = ?");
            $stmt->execute([$_POST['text'], $_POST['id_pub'], $this->sportif_id]);
            header("Location: dashboard.php");
            exit;
        }

        // Delete publication
        if (isset($_POST['action']) && $_POST['action'] === 'delete_pub') {
            $stmt = $this->publicationModel->getPdo()->prepare("DELETE FROM publication WHERE id_pub = ? AND id_user = ?");
            $stmt->execute([$_POST['id_pub'], $this->sportif_id]);
            header("Location: dashboard.php");
            exit;
        }
    }

    public function getData() {
        $data = [];
        try {
            $data['current_user'] = $this->userModel->getUserById($this->sportif_id);
            $publications = [];
            $pdo = $this->publicationModel->getPdo();
            $stmt_pubs = $pdo->prepare("SELECT * FROM publication WHERE id_user = ? ORDER BY date DESC");
            $stmt_pubs->execute([$this->sportif_id]);
            $pubs = $stmt_pubs->fetchAll();
            
            foreach($pubs as $p) {
                $stmt_c = $pdo->prepare("SELECT * FROM commentaire WHERE id_pub = ? ORDER BY date ASC");
                $stmt_c->execute([$p['id_pub']]);
                $p['commentaires'] = $stmt_c->fetchAll();
                $publications[] = $p;
            }
            $data['publications'] = $publications;
            $data['db_error'] = null;
        } catch (PDOException $e) {
            $data['current_user'] = null;
            $data['publications'] = [];
            $data['db_error'] = "Base de données non initialisée.";
        }
        return $data;
    }

    private function getPdo() {
        return Database::getConnection();
    }
}
?>