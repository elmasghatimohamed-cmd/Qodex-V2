<?php
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/Security.php';
require_once '../classes/User.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/auth/register.php');
    exit();
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['register_error'] = 'Token de sécurité invalide';
    header('Location: ../pages/auth/register.php');
    exit();
}

// Récupérer et nettoyer les données
$nom = Security::clean($_POST['nom'] ?? '');
$email = Security::clean($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$role = Security::clean($_POST['role'] ?? 'enseignant');

// Validation
if (empty($nom) || empty($email) || empty($password) || empty($confirmPassword)) {
    $_SESSION['register_error'] = 'Veuillez remplir tous les champs';
    header('Location: ../pages/auth/register.php');
    exit();
}

if (!Security::validateEmail($email)) {
    $_SESSION['register_error'] = 'Email invalide';
    header('Location: ../pages/auth/register.php');
    exit();
}

if (!Security::validatePassword($password)) {
    $_SESSION['register_error'] = 'Le mot de passe doit contenir au moins 8 caractères';
    header('Location: ../pages/auth/register.php');
    exit();
}

if ($password !== $confirmPassword) {
    $_SESSION['register_error'] = 'Les mots de passe ne correspondent pas';
    header('Location: ../pages/auth/register.php');
    exit();
}

if (!in_array($role, ['enseignant', 'etudiant'])) {
    $_SESSION['register_error'] = 'Rôle invalide';
    header('Location: ../pages/auth/register.php');
    exit();
}

// Créer l'utilisateur
$user = new User();
$result = $user->create($nom, $email, $password, $role);

if ($result) {
    $_SESSION['register_success'] = 'Compte créé avec succès ! Vous pouvez vous connecter.';
    header('Location: ../pages/auth/login.php');
    exit();
} else {
    $_SESSION['register_error'] = 'Erreur lors de la création du compte. Email déjà utilisé ?';
    header('Location: ../pages/auth/register.php');
    exit();
}