<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';

Security::requireTeacher();

if (!isset($_GET['token']) || !Security::verifyCSRFToken($_GET['token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

$quizId = intval($_GET['id'] ?? 0);
$teacherId = $_SESSION['user_id'];

if ($quizId <= 0) {
    $_SESSION['quiz_error'] = 'Quiz invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

$quizObj = new Quiz();
$result = $quizObj->delete($quizId, $teacherId);

if ($result) {
    $_SESSION['quiz_success'] = 'Quiz supprimé avec succès';
} else {
    $_SESSION['quiz_error'] = 'Erreur lors de la suppression du quiz ou accès refusé';
}

header('Location: ../../pages/teacher/quiz.php');
exit();