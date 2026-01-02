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
    require_once '../../classes/Result.php';

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Non connecté'
        ]);
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

    $data = json_decode(file_get_contents('php://input'), true);

    $quizId = isset($data['quiz_id']) ? (int) $data['quiz_id'] : 0;
    $answers = isset($data['answers']) ? $data['answers'] : [];

    if ($quizId <= 0 || empty($answers)) {
        echo json_encode([
            'success' => false,
            'error' => 'Données invalides'
        ]);
        exit();
    }

    $questionModel = new Question();
    $resultModel = new Result();

    $questions = $questionModel->getAllByQuiz($quizId);

    if (empty($questions)) {
        echo json_encode([
            'success' => false,
            'error' => 'Quiz introuvable'
        ]);
        exit();
    }

    $score = 0;
    $totalQuestions = count($questions);
    $results = [];

    foreach ($questions as $question) {
        $questionId = $question['id'];
        $userAnswer = isset($answers[$questionId]) ? (int) $answers[$questionId] : 0;
        $correctAnswer = (int) $question['correct_option'];

        $isCorrect = ($userAnswer === $correctAnswer);
        if ($isCorrect) {
            $score++;
        }

        $results[] = [
            'question_id' => $questionId,
            'question' => $question['question'],
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'is_correct' => $isCorrect,
            'options' => [
                1 => $question['option1'],
                2 => $question['option2'],
                3 => $question['option3'],
                4 => $question['option4']
            ]
        ];
    }

    $resultId = $resultModel->save($quizId, $_SESSION['user_id'], $score, $totalQuestions);

    if (!$resultId) {
        echo json_encode([
            'success' => false,
            'error' => 'Erreur lors de la sauvegarde du résultat'
        ]);
        exit();
    }

    $percentage = ($score / $totalQuestions) * 100;

    echo json_encode([
        'success' => true,
        'result_id' => $resultId,
        'score' => $score,
        'total_questions' => $totalQuestions,
        'percentage' => round($percentage, 2),
        'details' => $results
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