<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Publication.php';
require_once __DIR__ . '/../models/Commentaire.php';

class CoachController {
    private $userModel;
    private $publicationModel;
    private $commentaireModel;

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
                header("Location: index.php");
                exit;
            }
            $stmt = $this->publicationModel->getPdo()->prepare("INSERT INTO publication (id_user, text, date) VALUES (?, ?, NOW())");
            $stmt->execute([$_POST['id_user'], $_POST['text']]);
            header("Location: index.php");
            exit;
        }

        // Edit publication
        if (isset($_POST['action']) && $_POST['action'] === 'edit_pub') {
            $validation = $this->publicationModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: index.php");
                exit;
            }
            $stmt = $this->publicationModel->getPdo()->prepare("UPDATE publication SET text = ? WHERE id_pub = ?");
            $stmt->execute([$_POST['text'], $_POST['id_pub']]);
            header("Location: index.php");
            exit;
        }

        // Delete publication
        if (isset($_POST['action']) && $_POST['action'] === 'delete_pub') {
            $stmt = $this->publicationModel->getPdo()->prepare("DELETE FROM publication WHERE id_pub = ?");
            $stmt->execute([$_POST['id_pub']]);
            header("Location: index.php");
            exit;
        }

        // Add comment manually or by reply
        if (isset($_POST['action']) && in_array($_POST['action'], ['add_comment_manual', 'add_comment'], true)) {
            if ($_POST['action'] === 'add_comment_manual') {
                if (empty($_POST['id_pub'])) {
                    $_SESSION['error'] = "Veuillez sélectionner une publication.";
                    header("Location: index.php");
                    exit;
                }
            }
            $validation = $this->commentaireModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: index.php");
                exit;
            }
            $userId = $_POST['id_user'] ?? 1;
            $stmt = $this->commentaireModel->getPdo()->prepare("INSERT INTO commentaire (id_pub, id_user, text, date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_POST['id_pub'], $userId, $_POST['text']]);
            header("Location: index.php");
            exit;
        }

        // Edit comment
        if (isset($_POST['action']) && $_POST['action'] === 'edit_comment') {
            $validation = $this->commentaireModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: index.php");
                exit;
            }
            $stmt = $this->commentaireModel->getPdo()->prepare("UPDATE commentaire SET text = ? WHERE id_cmmnt = ?");
            $stmt->execute([$_POST['text'], $_POST['id_cmmnt']]);
            header("Location: index.php");
            exit;
        }

        // Delete comment
        if (isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
            $stmt = $this->commentaireModel->getPdo()->prepare("DELETE FROM commentaire WHERE id_cmmnt = ?");
            $stmt->execute([$_POST['id_cmmnt']]);
            header("Location: index.php");
            exit;
        }
    }

    public function getData() {
        $data = [];
        try {
            $publications = [];
            $pdo = $this->publicationModel->getPdo();
            $stmt_pubs = $pdo->prepare("SELECT p.*, u.nom, u.prenom FROM publication p JOIN `user` u ON p.id_user = u.user_id ORDER BY p.date DESC");
            $stmt_pubs->execute();
            $pubs = $stmt_pubs->fetchAll();
            
            foreach($pubs as $p) {
                $stmt_c = $pdo->prepare("SELECT c.*, u.nom, u.prenom FROM commentaire c JOIN `user` u ON c.id_user = u.user_id WHERE c.id_pub = ? ORDER BY c.date ASC");
                $stmt_c->execute([$p['id_pub']]);
                $p['commentaires'] = $stmt_c->fetchAll();
                $publications[] = $p;
            }
            $data['publications'] = $publications;
            // Fetch coach comments (assuming coach is user id 1)
            $stmt_comments = $pdo->prepare("SELECT c.*, u.nom, u.prenom FROM commentaire c JOIN `user` u ON c.id_user = u.user_id WHERE c.id_user = 1 ORDER BY c.date DESC");
            $stmt_comments->execute();
            $data['commentaires'] = $stmt_comments->fetchAll();
            $data['users'] = $this->userModel->getAllUsers(); // Assuming method exists
            $data['db_error'] = null;
        } catch (PDOException $e) {
            $data['publications'] = [];
            $data['commentaires'] = [];
            $data['users'] = [];
            $data['db_error'] = "Base de données non initialisée.";
        }
        return $data;
    }

    private function getPdo() {
        return Database::getConnection();
    }
}
?>