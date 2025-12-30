<?php
/**
 * Action: Modifier une question
 * Simple pour les débutants
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Question.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Vérifier la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Récupérer les données du formulaire
$questionId = intval($_POST['question_id'] ?? 0);
$quizId = intval($_POST['quiz_id'] ?? 0);
$question = Security::clean($_POST['question'] ?? '');
$option1 = Security::clean($_POST['option1'] ?? '');
$option2 = Security::clean($_POST['option2'] ?? '');
$option3 = Security::clean($_POST['option3'] ?? '');
$option4 = Security::clean($_POST['option4'] ?? '');
$correctOption = intval($_POST['correct_option'] ?? 0);
$teacherId = $_SESSION['user_id'];

// Validation des données
if ($questionId <= 0 || $quizId <= 0 || empty($question)) {
    $_SESSION['quiz_error'] = 'Données invalides';
    header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
    exit();
}

// Vérifier que l'enseignant est propriétaire du quiz
$quizObj = new Quiz();
if (!$quizObj->isOwner($quizId, $teacherId)) {
    $_SESSION['quiz_error'] = 'Accès refusé';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Mettre à jour la question
$questionObj = new Question();
$result = $questionObj->update($questionId, $question, $option1, $option2, $option3, $option4, $correctOption);

if ($result) {
    $_SESSION['quiz_success'] = 'Question modifiée avec succès';
} else {
    $_SESSION['quiz_error'] = 'Erreur lors de la modification';
}

// Rediriger vers la page d'édition du quiz
header('Location: ../../pages/teacher/quiz_edit.php?id=' . $quizId);
exit();

