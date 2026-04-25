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
        $this->sportif_id = $this->resolveSportifId();
    }

    public function handlePost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $redirectPath = $this->getSafeRedirectPath();
        $isFocusPage = in_array($redirectPath, ['demandes-entrainement.php', 'demandes-nutrition.php'], true);
        $action = $_POST['action'] ?? '';

        if ($isFocusPage && $action === 'edit_pub') {
            $_SESSION['error'] = "Sur cette page, la modification est désactivée.";
            header("Location: " . $redirectPath);
            exit;
        }

        // Add publication
        if ($action === 'add_pub') {
            $validation = $this->publicationModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: " . $redirectPath);
                exit;
            }
            try {
                $stmt = $this->publicationModel->getPdo()->prepare("INSERT INTO publication (id_user, text, date) VALUES (?, ?, NOW())");
                $stmt->execute([$this->sportif_id, $_POST['text']]);
            } catch (PDOException $e) {
                $_SESSION['error'] = "Impossible d'ajouter la publication: utilisateur invalide.";
                header("Location: " . $redirectPath);
                exit;
            }
            header("Location: " . $redirectPath);
            exit;
        }

        // Edit publication
        if ($action === 'edit_pub') {
            $validation = $this->publicationModel->validateText($_POST['text']);
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: " . $redirectPath);
                exit;
            }
            $stmt = $this->publicationModel->getPdo()->prepare("UPDATE publication SET text = ? WHERE id_pub = ? AND id_user = ?");
            $stmt->execute([$_POST['text'], $_POST['id_pub'], $this->sportif_id]);
            header("Location: " . $redirectPath);
            exit;
        }

        // Delete publication
        if ($action === 'delete_pub') {
            $stmt = $this->publicationModel->getPdo()->prepare("DELETE FROM publication WHERE id_pub = ? AND id_user = ?");
            $stmt->execute([$_POST['id_pub'], $this->sportif_id]);
            header("Location: " . $redirectPath);
            exit;
        }

        // Add comment by reply
        if ($action === 'add_comment') {
            $validation = $this->commentaireModel->validateText($_POST['text'] ?? '');
            if ($validation !== true) {
                $_SESSION['error'] = $validation;
                header("Location: " . $redirectPath);
                exit;
            }
            $stmt = $this->commentaireModel->getPdo()->prepare("INSERT INTO commentaire (id_pub, id_user, text, date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_POST['id_pub'], $this->sportif_id, $_POST['text']]);
            header("Location: " . $redirectPath);
            exit;
        }
    }

    public function getData() {
        $data = [];
        try {
            $data['current_user'] = $this->userModel->getUserById($this->sportif_id);
            $publications = [];
            $pdo = $this->publicationModel->getPdo();
            $focus = isset($_GET['focus']) ? strtolower(trim($_GET['focus'])) : '';
            if (!in_array($focus, ['entrainement', 'nutrition'], true)) {
                $focus = '';
            }
            $stmt_pubs = $pdo->prepare("SELECT * FROM publication WHERE id_user = ? ORDER BY date DESC");
            $stmt_pubs->execute([$this->sportif_id]);
            $pubs = $stmt_pubs->fetchAll();
            
            foreach($pubs as $p) {
                $sections = $this->extractRequestSections((string)($p['text'] ?? ''));
                if ($focus === 'entrainement' && $sections['entrainement'] === '') {
                    continue;
                }
                if ($focus === 'nutrition' && $sections['nutrition'] === '') {
                    continue;
                }
                $stmt_c = $pdo->prepare("SELECT * FROM commentaire WHERE id_pub = ? ORDER BY date ASC");
                $stmt_c->execute([$p['id_pub']]);
                $publicationComments = $stmt_c->fetchAll();
                foreach ($publicationComments as &$pubComment) {
                    $pubComment['text'] = $this->stripScopeMarker((string)($pubComment['text'] ?? ''));
                }
                unset($pubComment);
                $p['commentaires'] = $publicationComments;
                $publications[] = $p;
            }
            $data['publications'] = $publications;
            $data['db_error'] = null;
            $data['focus'] = $focus;
        } catch (PDOException $e) {
            $data['current_user'] = null;
            $data['publications'] = [];
            $data['db_error'] = "Base de données non initialisée.";
            $data['focus'] = '';
        }
        return $data;
    }

    private function getSafeRedirectPath() {
        $currentScript = basename($_SERVER['PHP_SELF'] ?? 'dashboard.php');
        $allowedScripts = ['dashboard.php', 'demandes-entrainement.php', 'demandes-nutrition.php'];

        if (in_array($currentScript, $allowedScripts, true)) {
            return $currentScript;
        }
        return 'dashboard.php';
    }

    private function extractRequestSections($text) {
        $normalizedText = str_replace("\r\n", "\n", (string)$text);
        $result = ['entrainement' => '', 'nutrition' => ''];

        if (preg_match('/Entra(?:î|i)nement\s*:[ \t]*(.*?)(?:\n\s*\n\s*Nutrition\s*:|$)/isu', $normalizedText, $trainingMatch)) {
            $result['entrainement'] = trim($trainingMatch[1]);
        }

        if (preg_match('/Nutrition\s*:[ \t]*(.*)$/isu', $normalizedText, $nutritionMatch)) {
            $result['nutrition'] = trim($nutritionMatch[1]);
        }

        return $result;
    }

    private function stripScopeMarker($text) {
        $globalMarker = '[[SRC:GLOBAL]] ';
        $focusMarker = '[[SRC:FOCUS]] ';
        if (strpos($text, $globalMarker) === 0) {
            return substr($text, strlen($globalMarker));
        }
        if (strpos($text, $focusMarker) === 0) {
            return substr($text, strlen($focusMarker));
        }
        return $text;
    }

    private function resolveSportifId() {
        // Keep configured id only if that user is a Sportif.
        $pdo = $this->publicationModel->getPdo();
        $stmtConfigured = $pdo->prepare("SELECT user_id FROM `user` WHERE user_id = ? AND role = 'Sportif' LIMIT 1");
        $stmtConfigured->execute([$this->sportif_id]);
        $configuredUser = $stmtConfigured->fetch();
        if ($configuredUser && isset($configuredUser['user_id'])) {
            return (int)$configuredUser['user_id'];
        }

        // Fallback to first user with role Sportif (not just first user in table).
        $stmtSportif = $pdo->query("SELECT user_id FROM `user` WHERE role = 'Sportif' ORDER BY user_id ASC LIMIT 1");
        $sportif = $stmtSportif->fetch();
        if ($sportif && isset($sportif['user_id'])) {
            return (int)$sportif['user_id'];
        }

        return 0;
    }

    private function getPdo() {
        return Database::getConnection();
    }
}
?>