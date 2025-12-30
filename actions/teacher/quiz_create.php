<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Question.php';

Security::requireTeacher();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/teacher/quiz.php');
    exit();
}

if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/teacher/quiz_create.php');
    exit();
}

$titre = Security::clean($_POST['titre'] ?? '');
$description = Security::clean($_POST['description'] ?? '');
$categorieId = intval($_POST['categorie_id'] ?? 0);
$teacherId = $_SESSION['user_id'];
$questions = $_POST['questions'] ?? [];

// Validation
if (empty($titre) || $categorieId <= 0) {
    $_SESSION['quiz_error'] = 'Titre et catégorie sont requis';
    header('Location: ../../pages/teacher/quiz_create.php');
    exit();
}

if (empty($questions)) {
    $_SESSION['quiz_error'] = 'Vous devez ajouter au moins une question';
    header('Location: ../../pages/teacher/quiz_create.php');
    exit();
}

// Utiliser une transaction pour garantir l'intégrité des données
$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Démarrer la transaction
    $conn->beginTransaction();

    // Créer le quiz
    $quizObj = new Quiz();
    $quizId = $quizObj->create($titre, $description, $categorieId, $teacherId);

    if (!$quizId) {
        throw new Exception('Erreur lors de la création du quiz');
    }

    // Ajouter les questions
    $questionObj = new Question();
    $questionsAdded = 0;

    foreach ($questions as $q) {
        $question = Security::clean($q['question'] ?? '');
        $option1 = Security::clean($q['option1'] ?? '');
        $option2 = Security::clean($q['option2'] ?? '');
        $option3 = Security::clean($q['option3'] ?? '');
        $option4 = Security::clean($q['option4'] ?? '');
        $correctOption = intval($q['correct_option'] ?? 0);

        if (
            empty($question) || empty($option1) || empty($option2) ||
            empty($option3) || empty($option4) || $correctOption < 1 || $correctOption > 4
        ) {
            continue;
        }

        $result = $questionObj->create($quizId, $question, $option1, $option2, $option3, $option4, $correctOption);
        if ($result) {
            $questionsAdded++;
        }
    }

    // Vérifier qu'au moins une question a été ajoutée
    if ($questionsAdded === 0) {
        throw new Exception('Aucune question valide n\'a été ajoutée');
    }

    // Tout s'est bien passé, valider la transaction
    $conn->commit();

    $_SESSION['quiz_success'] = "Quiz créé avec succès avec {$questionsAdded} question(s)";
    header('Location: ../../pages/teacher/quiz.php');
    exit();

} catch (Exception $e) {
    // En cas d'erreur, annuler toutes les modifications
    $conn->rollBack();

    $_SESSION['quiz_error'] = $e->getMessage();
    header('Location: ../../pages/teacher/quiz_create.php');
    exit();
}