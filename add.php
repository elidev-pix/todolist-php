<?php 

/**
 * Ajout de nouvelle tâche
 * Rattachée à l'utilisateur connecté
 */

require_once 'auth.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        http_response_code(403);
        die('Requête invalide (CSRF).');
    }

    $todo = trim($_POST['todo'] ?? '');

    if ($todo !== '') {
        $sql = 'INSERT INTO todoliste (todo_name, user_id) VALUES (:todo, :user_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':todo', $todo);
        $stmt->bindParam(':user_id', $current_user_id);
        $stmt->execute();
    }

    header('Location: index.php');
    exit;
}
