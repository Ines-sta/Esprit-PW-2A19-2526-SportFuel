<?php
// Controller: AlimentController

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';

$alimentModel = new Aliment($pdo);
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

// Traitement des actions
switch ($action) {

    case 'ajouter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $categorie = trim($_POST['categorie'] ?? '');
            $kcal_portion = floatval($_POST['kcal_portion'] ?? 0);
            $co2_impact = floatval($_POST['co2_impact'] ?? 0);
            $est_bio = isset($_POST['est_bio']) ? 1 : 0;
            $est_local = isset($_POST['est_local']) ? 1 : 0;

            // Validation côté serveur
            if (empty($nom)) {
                $error = "Le nom de l'aliment est obligatoire.";
            } elseif (strlen($nom) > 150) {
                $error = "Le nom ne doit pas dépasser 150 caractères.";
            } elseif (empty($categorie)) {
                $error = "La catégorie est obligatoire.";
            } elseif (strlen($categorie) > 100) {
                $error = "La catégorie ne doit pas dépasser 100 caractères.";
            } elseif ($kcal_portion <= 0) {
                $error = "Les calories doivent être un nombre positif.";
            } elseif ($co2_impact < 0) {
                $error = "L'impact CO₂ doit être un nombre positif.";
            } else {
                $alimentModel->ajouter($nom, $categorie, $kcal_portion, $co2_impact, $est_bio, $est_local);
                header('Location: aliment_controller.php?success=ajout');
                exit;
            }
        }
        break;

    case 'modifier':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $nom = trim($_POST['nom'] ?? '');
            $categorie = trim($_POST['categorie'] ?? '');
            $kcal_portion = floatval($_POST['kcal_portion'] ?? 0);
            $co2_impact = floatval($_POST['co2_impact'] ?? 0);
            $est_bio = isset($_POST['est_bio']) ? 1 : 0;
            $est_local = isset($_POST['est_local']) ? 1 : 0;

            if ($id <= 0) {
                $error = "Aliment invalide.";
            } elseif (empty($nom)) {
                $error = "Le nom de l'aliment est obligatoire.";
            } elseif (strlen($nom) > 150) {
                $error = "Le nom ne doit pas dépasser 150 caractères.";
            } elseif (empty($categorie)) {
                $error = "La catégorie est obligatoire.";
            } elseif (strlen($categorie) > 100) {
                $error = "La catégorie ne doit pas dépasser 100 caractères.";
            } elseif ($kcal_portion <= 0) {
                $error = "Les calories doivent être un nombre positif.";
            } elseif ($co2_impact < 0) {
                $error = "L'impact CO₂ doit être un nombre positif.";
            } else {
                $alimentModel->modifier($id, $nom, $categorie, $kcal_portion, $co2_impact, $est_bio, $est_local);
                header('Location: aliment_controller.php?success=modif');
                exit;
            }
        }
        break;

    case 'supprimer':
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $alimentModel->supprimer($id);
            header('Location: aliment_controller.php?success=suppr');
            exit;
        }
        break;
}

// Message de succès
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'ajout': $success = "Aliment ajouté avec succès."; break;
        case 'modif': $success = "Aliment modifié avec succès."; break;
        case 'suppr': $success = "Aliment supprimé avec succès."; break;
    }
}

// Récupérer les données pour la vue
$aliments = $alimentModel->listerTout();

// Récupérer l'aliment à modifier (si demandé)
$alimentEdit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $alimentEdit = $alimentModel->getById(intval($_GET['id']));
}

// Charger la vue
require_once __DIR__ . '/../views/aliments/aliments.php';
