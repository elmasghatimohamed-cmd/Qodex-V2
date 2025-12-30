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

$categoryId = intval($_POST['category_id'] ?? 0);
$nom = Security::clean($_POST['nom'] ?? '');
$description = Security::clean($_POST['description'] ?? '');
$teacherId = $_SESSION['user_id'];

if ($categoryId <= 0 || empty($nom)) {
    $_SESSION['category_error'] = 'Données invalides';
    header('Location: ../../pages/teacher/category_edit.php?id=' . $categoryId);
    exit();
}

$category = new Category();
$result = $category->update($categoryId, $nom, $description, $teacherId);

if ($result) {
    $_SESSION['category_success'] = 'Catégorie modifiée avec succès';
    header('Location: ../../pages/teacher/categories.php');
} else {
    $_SESSION['category_error'] = 'Erreur lors de la modification';
    header('Location: ../../pages/teacher/category_edit.php?id=' . $categoryId);
}
exit();