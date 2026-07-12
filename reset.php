<?php 

/**
 * Réinitialisation de la tâche
 * On garde le même id
 * start_date, end_date, duration sont supprimés - SET NULL
 * todo_reset est initialisé à 1, cette valeur sert de repère pour indiquer la réinitialisation
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

    $sql = 'UPDATE todoliste SET todo_reset = 1, start_date = NULL, end_date = NULL, start_time = NULL,
            end_time = NULL, start = NULL, end = NULL, duration = NULL
            WHERE todo_id = :todo_id AND user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
}

header('Location: index.php');
exit();
?>
