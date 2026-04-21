<?php
// Controller FO: Courses

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Course.php';

$courseModel = new Course($pdo);
$success = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ($action) {
    case 'toggle_achete':
        $id_course = intval($_GET['id_course'] ?? 0);
        $id_aliment = intval($_GET['id_aliment'] ?? 0);
        if ($id_course > 0 && $id_aliment > 0) {
            $courseModel->marquerAchete($id_course, $id_aliment);
            header('Location: course_controller.php?id=' . $id_course . '&success=achat');
            exit;
        }
        break;
}

if (isset($_GET['success']) && $_GET['success'] === 'achat') {
    $success = "Statut d'achat mis à jour.";
}

$courses = $courseModel->consulterCourses();

// Vue détail si id fourni
$courseDetail = null;
if (isset($_GET['id'])) {
    $courseDetail = $courseModel->getById(intval($_GET['id']));
}

// Charger la vue
require_once __DIR__ . '/../views/courses/courses.php';
