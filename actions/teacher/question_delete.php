<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Question.php';
require_once '../../classes/Quiz.php';

Security::requireTeacher();

if (!isset($_GET['token']) || !Security::verifyCSRFToken($_GET['token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

$questionId = intval($_GET['id'] ?? 0);
$quizId = intval($_GET['quiz_id'] ?? 0);
$teacherId = $_SESSION['user_id'];

// Vérifier que le quiz appartient à l'enseignant
$quizObj = new Quiz();
if (!$quizObj->isOwner($quizId, $teacherId)) {
    $_SESSION['quiz_error'] = 'Accès refusé';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Vérifier qu'il reste au moins 2 questions
$questionObj = new Question();
$questionsCount = $questionObj->countByQuiz($quizId);

if ($questionsCount <= 1) {
    $_SESSION['quiz_error'] = 'Impossible de supprimer la dernière question. Un quiz doit avoir au moins une question.';
    header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
    exit();
}

$result = $questionObj->delete($questionId);

if ($result) {
    $_SESSION['quiz_success'] = 'Question supprimée avec succès';
} else {
    $_SESSION['quiz_error'] = 'Erreur lors de la suppression de la question';
}

header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
exit();