<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';

Security::requireTeacher();

if (!isset($_GET['token']) || !Security::verifyCSRFToken($_GET['token'])) {
    $_SESSION['category_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/categories.php');
    exit();
}

$categoryId = intval($_GET['id'] ?? 0);
$teacherId = $_SESSION['user_id'];

if ($categoryId <= 0) {
    $_SESSION['category_error'] = 'Catégorie invalide';
    header('Location: ../../pages/teacher/categories.php');
    exit();
}

$category = new Category();

// Vérifier si la catégorie a des quiz
if ($category->hasQuizzes($categoryId)) {
    $_SESSION['category_error'] = 'Impossible de supprimer une catégorie contenant des quiz';
    header('Location: ../../pages/teacher/categories.php');
    exit();
}

$result = $category->delete($categoryId, $teacherId);

if ($result) {
    $_SESSION['category_success'] = 'Catégorie supprimée avec succès';
} else {
    $_SESSION['category_error'] = 'Erreur lors de la suppression de la catégorie';
}

header('Location: ../../pages/teacher/categories.php');
exit();