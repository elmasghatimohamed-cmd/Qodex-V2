<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once '../../config/database.php';
    require_once '../../classes/Database.php';

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

    $db = Database::getInstance();

    $sql = "SELECT c.*, COUNT(q.id) as quiz_count
            FROM categories c
            LEFT JOIN quiz q ON c.id = q.categorie_id AND q.is_active = 1
            GROUP BY c.id
            HAVING quiz_count > 0
            ORDER BY c.nom ASC";

    $result = $db->query($sql);
    $categoriesWithQuizzes = $result->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $categoriesWithQuizzes,
        'count' => count($categoriesWithQuizzes)
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