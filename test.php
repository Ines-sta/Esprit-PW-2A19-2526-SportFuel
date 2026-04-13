<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Database.php';

try {
    $pdo = Database::getConnection();
    echo "Connection successful!\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
