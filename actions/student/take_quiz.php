<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once '../../config/database.php';
    require_once '../../classes/Database.php';
    require_once '../../classes/Security.php';
    require_once '../../classes/Quiz.php';
    require_once '../../classes/Question.php';

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false]);
        exit;
    }

    if ($_SESSION['user_role'] !== 'etudiant') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Non autorisé'
        ]);
        exit;
    }
    $quizId = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

    if ($quizId <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'ID de quiz invalide'
        ]);
        exit();
    }

    $quizModel = new Quiz();
    $questionModel = new Question();

    $quiz = $quizModel->getById($quizId);

    if (!$quiz) {
        echo json_encode([
            'success' => false,
            'error' => 'Quiz introuvable'
        ]);
        exit();
    }

    if (!$quiz['is_active']) {
        echo json_encode([
            'success' => false,
            'error' => 'Ce quiz n\'est plus disponible'
        ]);
        exit();
    }

    $questions = $questionModel->getAllByQuiz($quizId);

    if (empty($questions)) {
        echo json_encode([
            'success' => false,
            'error' => 'Ce quiz ne contient aucune question'
        ]);
        exit();
    }

    $questionsFormatted = array_map(function ($q) {
        return [
            'id' => $q['id'],
            'question' => $q['question'],
            'options' => [
                1 => $q['option1'],
                2 => $q['option2'],
                3 => $q['option3'],
                4 => $q['option4']
            ]
        ];
    }, $questions);

    echo json_encode([
        'success' => true,
        'quiz' => [
            'id' => $quiz['id'],
            'titre' => $quiz['titre'],
            'description' => $quiz['description'],
            'categorie_nom' => $quiz['categorie_nom']
        ],
        'questions' => $questionsFormatted,
        'total_questions' => count($questions)
    ], JSON_UNESCAPED_UNICODE);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit();
}