<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once '../../config/database.php';
    require_once '../../classes/Database.php';
    require_once '../../classes/Question.php';

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

    $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

    if ($categoryId <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'ID de catégorie invalide'
        ]);
        exit();
    }

    $db = Database::getInstance();
    $questionModel = new Question();

    $sql = "SELECT q.id, q.titre, q.description, q.categorie_id, 
                   c.nom as categorie_nom,
                   q.created_at
            FROM quiz q
            INNER JOIN categories c ON q.categorie_id = c.id
            WHERE q.categorie_id = ? AND q.is_active = 1
            ORDER BY q.created_at DESC";

    $result = $db->query($sql, [$categoryId]);
    $quizzes = $result->fetchAll();

    foreach ($quizzes as &$quiz) {
        $quiz['questions_count'] = $questionModel->countByQuiz($quiz['id']);
    }

    echo json_encode([
        'success' => true,
        'data' => $quizzes,
        'count' => count($quizzes),
        'category_id' => $categoryId
    ], JSON_UNESCAPED_UNICODE);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
    exit();
}