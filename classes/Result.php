<?php
/**
 * Classe Result
 * Gère les résultats des quiz (US7 - Voir ses résultats)
 * 
 * SÉCURITÉ: L'utilisateur ne peut voir QUE ses propres résultats
 */

class Result {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère les résultats d'un étudiant (ses propres résultats SEULEMENT)
     * @param int $etudiantId - L'ID de l'étudiant
     * @return array - Liste des résultats
     */
    public function getMyResults($etudiantId) {
        $sql = "SELECT r.*, q.titre as quiz_titre, c.nom as categorie_nom
                FROM results r
                LEFT JOIN quiz q ON r.quiz_id = q.id
                LEFT JOIN categories c ON q.categorie_id = c.id
                WHERE r.etudiant_id = ?
                ORDER BY r.created_at DESC";
        
        $result = $this->db->query($sql, [$etudiantId]);
        return $result->fetchAll();
    }
    
    /**
     * Récupère un résultat par ID (vérifie que c'est bien le propriétaire)
     * @param int $resultId
     * @param int $etudiantId
     * @return array|false
     */
    public function getById($resultId, $etudiantId) {
        $sql = "SELECT r.*, q.titre as quiz_titre
                FROM results r
                LEFT JOIN quiz q ON r.quiz_id = q.id
                WHERE r.id = ? AND r.etudiant_id = ?";
        
        $result = $this->db->query($sql, [$resultId, $etudiantId]);
        return $result->fetch();
    }
    
    /**
     * Enregistre un nouveau résultat
     * @param int $quizId
     * @param int $etudiantId
     * @param int $score
     * @param int $totalQuestions
     * @return int|false
     */
    public function save($quizId, $etudiantId, $score, $totalQuestions) {
        $sql = "INSERT INTO results (quiz_id, etudiant_id, score, total_questions, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        try {
            $this->db->query($sql, [$quizId, $etudiantId, $score, $totalQuestions]);
            return $this->db->getConnection()->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Calcule les statistiques d'un étudiant
     * @param int $etudiantId
     * @return array
     */
    public function getMyStats($etudiantId) {
        $sql = "SELECT 
                    COUNT(*) as total_quiz,
                    AVG(score / total_questions * 100) as moyenne,
                    MAX(score / total_questions * 100) as meilleur_score
                FROM results
                WHERE etudiant_id = ?";
        
        $result = $this->db->query($sql, [$etudiantId]);
        return $result->fetch();
    }
}
