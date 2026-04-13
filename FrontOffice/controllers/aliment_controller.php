<?php
// Controller FO: Aliments (lecture seule)

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';
require_once __DIR__ . '/../models/CategorieAlimentaire.php';

$alimentModel = new Aliment($pdo);
$categorieModel = new CategorieAlimentaire($pdo);

$aliments = $alimentModel->listerTout();
$categories = $categorieModel->listerTout();

// Charger la vue
require_once __DIR__ . '/../views/aliments/aliments.php';
