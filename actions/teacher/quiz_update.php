<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';

Security::requireTeacher();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

$quizId = intval($_POST['quiz_id'] ?? 0);
$titre = Security::clean($_POST['titre'] ?? '');
$description = Security::clean($_POST['description'] ?? '');
$categorieId = intval($_POST['categorie_id'] ?? 0);
$teacherId = $_SESSION['user_id'];

if ($quizId <= 0 || empty($titre) || $categorieId <= 0) {
    $_SESSION['quiz_error'] = 'Données invalides';
    header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
    exit();
}

$quizObj = new Quiz();
$result = $quizObj->update($quizId, $titre, $description, $categorieId, $teacherId);

if ($result) {
    $_SESSION['quiz_success'] = 'Quiz modifié avec succès';
} else {
    $_SESSION['quiz_error'] = 'Erreur lors de la modification ou accès refusé';
}

header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
exit();