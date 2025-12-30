<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';

Security::requireTeacher();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/teacher/categories.php');
    exit();
}

if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['category_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/categories.php');
    exit();
}

$nom = Security::clean($_POST['nom'] ?? '');
$description = Security::clean($_POST['description'] ?? '');
$teacherId = $_SESSION['user_id'];

if (empty($nom)) {
    $_SESSION['category_error'] = 'Le nom de la catégorie est requis';
    header('Location: ../../pages/teacher/categories.php');
    exit();
}

$category = new Category();
$result = $category->create($nom, $description, $teacherId);

if ($result) {
    $_SESSION['category_success'] = 'Catégorie créée avec succès';
} else {
    $_SESSION['category_error'] = 'Erreur lors de la création de la catégorie';
}

header('Location: ../../pages/teacher/categories.php');
exit();