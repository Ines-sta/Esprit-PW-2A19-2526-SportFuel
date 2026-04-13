<?php
require_once __DIR__ . '/../models/Database.php';
$pdo = Database::getConnection();
$columns = $pdo->query("SHOW COLUMNS FROM commentaire")->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . ' ' . $col['Type'] . '\n';
}
?>