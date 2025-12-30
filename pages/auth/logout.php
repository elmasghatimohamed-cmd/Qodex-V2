<?php
/**
 * Page: Déconnexion
 * Déconnecte l'utilisateur et redirige vers la page de connexion
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/User.php';

// Vérifier le token CSRF pour la déconnexion
if (isset($_GET['token']) && Security::verifyCSRFToken($_GET['token'])) {
    $user = new User();
    $user->logout();
}

// Rediriger vers la page de connexion
header('Location: login.php');
exit();
