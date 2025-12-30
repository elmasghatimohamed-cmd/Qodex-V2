<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Question.php';
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
$question = Security::clean($_POST['question'] ?? '');
$option1 = Security::clean($_POST['option1'] ?? '');
$option2 = Security::clean($_POST['option2'] ?? '');
$option3 = Security::clean($_POST['option3'] ?? '');
$option4 = Security::clean($_POST['option4'] ?? '');
$correctOption = intval($_POST['correct_option'] ?? 0);
$teacherId = $_SESSION['user_id'];

// Vérifier que le quiz appartient à l'enseignant
$quizObj = new Quiz();
if (!$quizObj->isOwner($quizId, $teacherId)) {
    $_SESSION['quiz_error'] = 'Accès refusé';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Validation
if (
    empty($question) || empty($option1) || empty($option2) ||
    empty($option3) || empty($option4) || $correctOption < 1 || $correctOption > 4
) {
    $_SESSION['quiz_error'] = 'Tous les champs sont requis et la réponse correcte doit être entre 1 et 4';
    header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
    exit();
}

$questionObj = new Question();
$result = $questionObj->create($quizId, $question, $option1, $option2, $option3, $option4, $correctOption);

if ($result) {
    $_SESSION['quiz_success'] = 'Question ajoutée avec succès';
} else {
    $_SESSION['quiz_error'] = 'Erreur lors de l\'ajout de la question';
}

header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
exit();