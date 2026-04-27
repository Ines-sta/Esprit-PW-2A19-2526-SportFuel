<?php
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $database = new Database();
    $pdo = $database->getPDO();

    $programmesOnly = isset($_GET['programmes']) && $_GET['programmes'] === '1';

    $sql = "SELECT id_entrainement, id_utilisateur, titre, date_entrainement, duree_totale, notes_globales, statut
            FROM entrainements ";
    if ($programmesOnly) {
        $sql .= "WHERE notes_globales LIKE '__PROGRAMME__|%' ";
    }
    $sql .= "ORDER BY date_entrainement DESC, id_entrainement DESC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $rows
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
