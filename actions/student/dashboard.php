<?php
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    require_once '../../config/database.php';
    require_once '../../classes/Database.php';
    require_once '../../classes/Security.php';
    require_once '../../classes/Result.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'etudiant') {
        echo json_encode([
            'success' => false,
            'error' => 'Non autorisé'
        ]);
        exit();
    }

    $studentId = $_SESSION['user_id'];
    $resultModel = new Result();
    $db = Database::getInstance();

    $stats = $resultModel->getMyStats($studentId);

    $sql = "SELECT r.*, q.titre as quiz_titre, c.nom as categorie_nom
            FROM results r
            LEFT JOIN quiz q ON r.quiz_id = q.id
            LEFT JOIN categories c ON q.categorie_id = c.id
            WHERE r.etudiant_id = ?
            ORDER BY r.created_at DESC
            LIMIT 5";
    $result = $db->query($sql, [$studentId]);
    $recentResults = $result->fetchAll();

    $sql = "SELECT r.score, r.total_questions, r.created_at
            FROM results r
            WHERE r.etudiant_id = ?
            ORDER BY r.created_at DESC
            LIMIT 10";
    $result = $db->query($sql, [$studentId]);
    $progressData = array_reverse($result->fetchAll());

    $sql = "SELECT COUNT(DISTINCT q.id) as count
            FROM quiz q
            INNER JOIN questions qu ON q.id = qu.quiz_id
            WHERE q.is_active = 1
            GROUP BY q.id
            HAVING COUNT(qu.id) > 0";
    $result = $db->query($sql);
    $availableQuizzes = $result->rowCount();

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_results' => $recentResults,
        'progress_data' => $progressData,
        'available_quizzes' => $availableQuizzes
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