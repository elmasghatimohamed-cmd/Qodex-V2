<?php
/**
 * Action: Connexion
 * Traite le formulaire de connexion
 */

require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/Security.php';
require_once '../classes/User.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/auth/login.php');
    exit();
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['login_error'] = 'Token de sécurité invalide';
    header('Location: ../pages/auth/login.php');
    exit();
}

// Récupérer et nettoyer les données
$email = Security::clean($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Veuillez remplir tous les champs';
    header('Location: ../pages/auth/login.php');
    exit();
}

if (!Security::validateEmail($email)) {
    $_SESSION['login_error'] = 'Email invalide';
    header('Location: ../pages/auth/login.php');
    exit();
}

// Tentative de connexion
$user = new User();
$result = $user->login($email, $password);

if ($result) {
    // Rediriger selon le rôle
    if ($_SESSION['user_role'] === 'enseignant') {
        header('Location: ../pages/teacher/dashboard.php');
    } else {
        header('Location: ../pages/student/dashboard.php');
    }
    exit();
} else {
    $_SESSION['login_error'] = 'Email ou mot de passe incorrect';
    header('Location: ../pages/auth/login.php');
    exit();
}