<?php
/**
 * Page: Connexion
 * Permet aux utilisateurs de se connecter
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';

// Si déjà connecté, rediriger vers le dashboard
if (Security::isLoggedIn()) {
    if ($_SESSION['user_role'] === 'enseignant') {
        header('Location: ../teacher/dashboard.php');
    } else {
        header('Location: ../student/dashboard.php');
    }
    exit();
}

// Récupérer les messages d'erreur et de succès
$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['register_success'] ?? '';
unset($_SESSION['login_error'], $_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - QuizMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <i class="fas fa-graduation-cap text-6xl text-indigo-600 mb-4"></i>
            <h1 class="text-3xl font-bold text-gray-900">QuizMaster</h1>
            <p class="text-gray-600 mt-2">Connectez-vous à votre compte</p>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form action="../../actions/login_action.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input type="email" name="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="votre@email.com">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-lock mr-2"></i>Mot de passe
                </label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
            </button>
        </form>

        <!-- Lien inscription -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Pas encore de compte ? 
                <a href="register.php" class="text-indigo-600 font-semibold hover:text-indigo-700">
                    S'inscrire
                </a>
            </p>
        </div>
    </div>
</body>
</html>
