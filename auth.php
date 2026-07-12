<?php

/**
 * Garde d'authentification
 * À inclure en tout début de chaque page nécessitant une connexion (index.php, add.php, etc.)
 * Redirige vers login.php si l'utilisateur n'est pas connecté
 */

require_once 'cnx.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Régénère périodiquement l'identifiant de session pour limiter les risques de fixation de session
if (empty($_SESSION['last_regen'])) {
    $_SESSION['last_regen'] = time();
} elseif (time() - $_SESSION['last_regen'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

$current_user_id = $_SESSION['user_id'];
