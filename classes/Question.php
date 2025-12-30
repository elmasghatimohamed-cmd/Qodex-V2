<?php
/**
 * Classe Question
 * Gère les opérations CRUD sur les questions
 */

class Question {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Crée une nouvelle question
  
    public function create($quizId, $question, $option1, $option2, $option3, $option4, $correctOption) {
        if (empty($quizId) || empty($question) || $correctOption < 1 || $correctOption > 4) {
            return false;
        }
        
        $sql = "INSERT INTO questions (quiz_id, question, option1, option2, option3, option4, correct_option) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        try {
            $this->db->query($sql, [
                $quizId, $question, $option1, $option2, $option3, $option4, $correctOption
            ]);
            return $this->db->getConnection()->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Récupère toutes les questions d'un quiz
    
    public function getAllByQuiz($quizId) {
        $sql = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC";
        $result = $this->db->query($sql, [$quizId]);
        return $result->fetchAll();
    }
    
    // Récupère une question par ID
     
    public function getById($id) {
        $sql = "SELECT * FROM questions WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result->fetch();
    }
    
    // Met à jour une question
     
    public function update($id, $question, $option1, $option2, $option3, $option4, $correctOption) {
        if ($correctOption < 1 || $correctOption > 4) {
            return false;
        }
        
        $sql = "UPDATE questions 
                SET question = ?, option1 = ?, option2 = ?, option3 = ?, 
                    option4 = ?, correct_option = ? 
                WHERE id = ?";
        try {
            $this->db->query($sql, [
                $question, $option1, $option2, $option3, $option4, $correctOption, $id
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Supprime une question
     
    public function delete($id) {
        $sql = "DELETE FROM questions WHERE id = ?";
        try {
            $this->db->query($sql, [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Supprime toutes les questions d'un quiz
     
    public function deleteAllByQuiz($quizId) {
        $sql = "DELETE FROM questions WHERE quiz_id = ?";
        try {
            $this->db->query($sql, [$quizId]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Compte le nombre de questions d'un quiz
     
    public function countByQuiz($quizId) {
        $sql = "SELECT COUNT(*) as count FROM questions WHERE quiz_id = ?";
        $result = $this->db->query($sql, [$quizId]);
        $data = $result->fetch();
        return (int)$data['count'];
    }
}
