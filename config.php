<?php
// Fichier: config.php
$host = '127.0.0.1';
$dbname = 'sportfueldb';
$username = 'root';
$password = ''; // Par défaut dans WAMP/XAMPP
$port = '3306';

try {
    // Connexion principale avec base de données (si elle existe)
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la base n'existe pas, on lève une erreur (sauf dans le setup de base de données)
    // Ne pas crasher complètement ici car le setup a besoin d'exister sans la base initialement
    if (!defined('SETUP_MODE_ACTIVE')) {
        die("Erreur de connexion à la base de données : " . $e->getMessage() . "<br>Veuillez exécuter setup_db.php en premier.");
    }
}
?>
