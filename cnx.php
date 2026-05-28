<?php 

/**
 * Connexion à la base de données (PhpMyAdmin)
 */

$host     = '127.0.0.1:3307';
$dbname   = 'todo';
$username = 'root';
$password = '';

$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch(PDOException $e) {
    // Le die() affiche le message ET arrête immédiatement l'exécution du code
    die('Erreur critique de connexion à la base de données : ' . $e->getMessage());
}

?>