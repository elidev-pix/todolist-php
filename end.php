<?php 

/**
 * Ajout de la date de fin au moment du clique sur l'icône de fin de tâche
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

    $end_date = (new DateTime())->format('Y/m/d');
    $end_time = (new DateTime())->format('H:i:s');
    $end = (new DateTime())->format('Y/m/d H:i:s');

    $stmt = $pdo->prepare("SELECT start FROM todoliste WHERE todo_id = :todo_id AND user_id = :user_id");
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['start']) {

        $start = $row['start'];

        $time_start = strtotime($start);
        $time_end = strtotime($end);
        $time = round(($time_end - $time_start));

        if ($time > 31104000) {
            $duration = round($time / 31104000) . ' an';
        } elseif ($time > 2592000) {
            $duration = round($time / 2592000) . ' mois';
        } elseif ($time > 86400) {
            $duration = round($time / 86400) . ' jours';
        } elseif ($time > 3600) {
            $duration = round($time / 3600) . ' h';
        } elseif ($time > 60) {
            $duration = round($time / 60) . ' mn';
        } else {
            $duration = $time . ' s';
        }

        $sql = 'UPDATE todoliste SET end_date = :end_date, end_time = :end_time, end = :end, duration = :duration
                WHERE todo_id = :todo_id AND user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':todo_id', $todo_id);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':end', $end);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':user_id', $current_user_id);
        $stmt->execute();
    }
}

header('Location: index.php');
exit();

?>
