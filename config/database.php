<?php
/**
 * Configuration de la base de données
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'school_managment');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

// Configuration des sessions sécurisées
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mettre à 1 si HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Durée de vie de la session (30 minutes)
ini_set('session.gc_maxlifetime', 1800);
session_set_cookie_params(1800);

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Régénération de l'ID de session pour prévenir le session hijacking
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}