<?php 

/**
 * Suppression d'une tâche à partir de son id
 * Vérifie que la tâche appartient bien à l'utilisateur connecté
 */

require_once 'auth.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        http_response_code(403);
        die('Requête invalide (CSRF).');
    }

    $todo_id = $_POST['todo_id'];

    $sql = 'DELETE FROM todoliste WHERE todo_id = :todo_id AND user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
}

header('Location: index.php');
exit();
?>
