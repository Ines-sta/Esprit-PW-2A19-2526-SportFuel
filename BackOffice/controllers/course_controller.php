<?php
// Controller: CourseController (BackOffice)

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Aliment.php';

$courseModel = new Course($pdo);
$alimentModel = new Aliment($pdo);
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

// Statuts autorisés
$statutsAutorises = ['Non démarrée', 'En cours', 'Complétée'];

// Traitement des actions
switch ($action) {

    case 'ajouter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_utilisateur = intval($_POST['id_utilisateur'] ?? 1);
            $date = trim($_POST['date'] ?? '');
            $statut = trim($_POST['statut'] ?? '');

            if (empty($date)) {
                $error = "La date est obligatoire.";
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $error = "Format de date invalide (AAAA-MM-JJ).";
            } elseif (empty($statut)) {
                $error = "Le statut est obligatoire.";
            } elseif (!in_array($statut, $statutsAutorises)) {
                $error = "Statut invalide.";
            } else {
                $courseModel->genererListeCourses($id_utilisateur, $date, $statut);
                header('Location: course_controller.php?success=ajout');
                exit;
            }
        }
        break;

    case 'modifier':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $id_utilisateur = intval($_POST['id_utilisateur'] ?? 1);
            $date = trim($_POST['date'] ?? '');
            $statut = trim($_POST['statut'] ?? '');

            if ($id <= 0) {
                $error = "Course invalide.";
            } elseif (empty($date)) {
                $error = "La date est obligatoire.";
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $error = "Format de date invalide (AAAA-MM-JJ).";
            } elseif (empty($statut)) {
                $error = "Le statut est obligatoire.";
            } elseif (!in_array($statut, $statutsAutorises)) {
                $error = "Statut invalide.";
            } else {
                $courseModel->modifier($id, $id_utilisateur, $date, $statut);
                header('Location: course_controller.php?action=voir&id=' . $id . '&success=modif');
                exit;
            }
        }
        break;

    case 'supprimer':
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $courseModel->supprimer($id);
            header('Location: course_controller.php?success=suppr');
            exit;
        }
        break;

    case 'ajouter_article':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_course = intval($_POST['id_course'] ?? 0);
            $id_aliment = intval($_POST['id_aliment'] ?? 0);
            $quantite = floatval($_POST['quantite'] ?? 0);

            if ($id_course <= 0) {
                $error = "Course invalide.";
            } elseif ($id_aliment <= 0) {
                $error = "Veuillez sélectionner un aliment.";
            } elseif ($quantite <= 0) {
                $error = "La quantité doit être un nombre positif.";
            } else {
                $courseModel->ajouterArticle($id_course, $id_aliment, $quantite);
                header('Location: course_controller.php?action=voir&id=' . $id_course . '&success=article_ajout');
                exit;
            }
        }
        break;

    case 'supprimer_article':
        $id_course = intval($_GET['id_course'] ?? 0);
        $id_aliment = intval($_GET['id_aliment'] ?? 0);
        if ($id_course > 0 && $id_aliment > 0) {
            $courseModel->supprimerArticle($id_course, $id_aliment);
            header('Location: course_controller.php?action=voir&id=' . $id_course . '&success=article_suppr');
            exit;
        }
        break;
}

// Message de succès
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'ajout': $success = "Liste de courses créée avec succès."; break;
        case 'modif': $success = "Liste de courses modifiée avec succès."; break;
        case 'suppr': $success = "Liste de courses supprimée avec succès."; break;
        case 'article_ajout': $success = "Article ajouté à la liste."; break;
        case 'article_suppr': $success = "Article retiré de la liste."; break;
    }
}

// Récupérer les données
$courses = $courseModel->listerTout();
$aliments = $alimentModel->listerTout();

// Vue détail d'une course
$courseDetail = null;
if (isset($_GET['action']) && $_GET['action'] === 'voir' && isset($_GET['id'])) {
    $courseDetail = $courseModel->getById(intval($_GET['id']));
}

// Modifier une course (pré-remplir le formulaire)
$courseEdit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $courseEdit = $courseModel->getById(intval($_GET['id']));
}

// Charger la vue
require_once __DIR__ . '/../views/courses/courses.php';
