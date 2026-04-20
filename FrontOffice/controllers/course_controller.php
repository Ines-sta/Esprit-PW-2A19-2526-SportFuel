<?php
// Controller FO: Courses (lecture seule)

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Course.php';

$courseModel = new Course($pdo);

$courses = $courseModel->consulterCourses();

// Vue détail si id fourni
$courseDetail = null;
if (isset($_GET['id'])) {
    $courseDetail = $courseModel->getById(intval($_GET['id']));
}

// Charger la vue
require_once __DIR__ . '/../views/courses/courses.php';
