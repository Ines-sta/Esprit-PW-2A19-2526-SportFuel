<?php
// Controller: CategorieAlimentaireController

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/CategorieAlimentaire.php';

$categorieModel = new CategorieAlimentaire($pdo);
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

// Traitement des actions
switch ($action) {

    case 'ajouter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validation côté serveur
            if (empty($nom)) {
                $error = "Le nom de la catégorie est obligatoire.";
            } elseif (strlen($nom) > 100) {
                $error = "Le nom ne doit pas dépasser 100 caractères.";
            } else {
                $categorieModel->ajouter($nom, $description);
                header('Location: categorie_controller.php?success=ajout');
                exit;
            }
        }
        break;

    case 'modifier':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($id <= 0) {
                $error = "Catégorie invalide.";
            } elseif (empty($nom)) {
                $error = "Le nom de la catégorie est obligatoire.";
            } elseif (strlen($nom) > 100) {
                $error = "Le nom ne doit pas dépasser 100 caractères.";
            } else {
                $categorieModel->modifier($id, $nom, $description);
                header('Location: categorie_controller.php?success=modif');
                exit;
            }
        }
        break;

    case 'supprimer':
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $categorieModel->supprimer($id);
            header('Location: categorie_controller.php?success=suppr');
            exit;
        }
        break;
}

// Message de succès
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'ajout': $success = "Catégorie ajoutée avec succès."; break;
        case 'modif': $success = "Catégorie modifiée avec succès."; break;
        case 'suppr': $success = "Catégorie supprimée avec succès."; break;
    }
}

// Récupérer les données pour la vue
$categories = $categorieModel->listerTout();

// Récupérer la catégorie à modifier (si demandé)
$categorieEdit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $categorieEdit = $categorieModel->getById(intval($_GET['id']));
}

// Charger la vue
require_once __DIR__ . '/../views/categories/categories.php';
