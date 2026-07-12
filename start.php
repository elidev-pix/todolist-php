<?php 

/**
 * Ajout de la date de début de la tâche au moment du clique sur l'icone en forme de +
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
    $now = new DateTime();
    $start_date = $now->format('Y/m/d');
    $start_time = $now->format('H:i:s');
    $start = $now->format('Y/m/d H:i:s');

    $sql = 'UPDATE todoliste SET start_date = :start_date, start_time = :start_time, start = :start, todo_reset = 0
            WHERE todo_id = :todo_id AND user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':start', $start);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();

    header('Location: index.php');
    exit();
}

?>
