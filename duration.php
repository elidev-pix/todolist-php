<?php 

/**
 * Calcul du temps écoulé entre le début et la fin de la tâche
 * Duration est en secondes - Utilisation des fonctions strtotime
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

    $stmt = $pdo->prepare("SELECT start_date, end_date FROM todoliste WHERE todo_id = :todo_id AND user_id = :user_id");
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];

        if ($start_date && $end_date) {

            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $time = round(($end - $start));

            if ($time > 86400) {
                $duration = $time / 86400;
            } elseif ($time > 3600) {
                $duration = $time / 3600;
            } elseif ($time > 60) {
                $duration = $time / 60;
            } else {
                $duration = $time;
            }

            $sql = 'UPDATE todoliste SET duration = :duration WHERE todo_id = :todo_id AND user_id = :user_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':todo_id', $todo_id);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':user_id', $current_user_id);
            $stmt->execute();

        } else {
            error_log("Erreur");
        }
    }
}

header('Location: index.php');
exit();
?>
