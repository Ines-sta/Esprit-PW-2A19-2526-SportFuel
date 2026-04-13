<?php
// Fichier: setup_db.php

$host = '127.0.0.1';
$dbname = 'sportfueldb';
$username = 'root';
$password = '';
$port = '3306';
define('SETUP_MODE_ACTIVE', true);

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    echo "<h3>Setup Base de Données</h3>";

    // Table Publication
    $pdo->exec("CREATE TABLE IF NOT EXISTS publication (
        id_pub INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT,
        text TEXT,
        date DATETIME,
        FOREIGN KEY (id_user) REFERENCES User(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "<p>Table 'publication' vérifiée/créée.</p>";

    // Table Commentaire
    $pdo->exec("CREATE TABLE IF NOT EXISTS commentaire (
        id_cmmnt INT AUTO_INCREMENT PRIMARY KEY,
        id_pub INT,
        text TEXT,
        date DATETIME,
        FOREIGN KEY (id_pub) REFERENCES publication(id_pub) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "<p>Table 'commentaire' vérifiée/créée.</p>";

    echo "<p><strong>Opération terminée avec succès !</strong></p>";
} catch (PDOException $e) {
    die("Erreur SQL: " . $e->getMessage());
}
?>
