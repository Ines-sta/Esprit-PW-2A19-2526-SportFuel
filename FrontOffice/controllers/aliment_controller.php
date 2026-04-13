<?php
// Controller FO: Aliments (lecture seule)

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';

$alimentModel = new Aliment($pdo);

$aliments = $alimentModel->listerTout();

// Charger la vue
require_once __DIR__ . '/../views/aliments/aliments.php';
