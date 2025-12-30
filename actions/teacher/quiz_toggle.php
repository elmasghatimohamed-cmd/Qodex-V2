<?php
/**
 * Action: Activer ou désactiver un quiz
 * Simple pour les débutants
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Récupérer les paramètres
$quizId = intval($_GET['id'] ?? 0);
$token = $_GET['token'] ?? '';
$teacherId = $_SESSION['user_id'];

// Vérifier le token CSRF
if (!Security::verifyCSRFToken($token)) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Vérifier que l'ID est valide
if ($quizId <= 0) {
    $_SESSION['quiz_error'] = 'Quiz invalide';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Créer l'objet Quiz
$quizObj = new Quiz();

// Récupérer le quiz actuel
$quiz = $quizObj->getById($quizId);

if (!$quiz) {
    $_SESSION['quiz_error'] = 'Quiz non trouvé';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Vérifier que l'enseignant est propriétaire du quiz
if (!$quizObj->isOwner($quizId, $teacherId)) {
    $_SESSION['quiz_error'] = 'Accès refusé';
    header('Location: ../../pages/teacher/quiz.php');
    exit();
}

// Inverser le statut (si actif -> inactif, si inactif -> actif)
$newStatus = $quiz['is_active'] ? 0 : 1;

// Mettre à jour le statut
$result = $quizObj->toggleActive($quizId, $newStatus, $teacherId);

if ($result) {
    $statusText = $newStatus ? 'activé' : 'désactivé';
    $_SESSION['quiz_success'] = 'Quiz ' . $statusText . ' avec succès';
} else {
    $_SESSION['quiz_error'] = 'Erreur lors du changement de statut';
}

// Rediriger vers la page des quiz
header('Location: ../../pages/teacher/quiz.php');
exit();

