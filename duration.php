<?php 

/**
 * Calcul du temps écoulé entre le début et la fin de la tâche
 * Duration est en secondes - Utilisation des fonctions strtotime
 */
require_once 'cnx.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todo_id = $_POST['todo_id']; 
    
    $stmt=$pdo->prepare("SELECT start_date, end_date FROM todoliste WHERE todo_id= :todo_id");
    $stmt->bindParam(':todo_id',$todo_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $start_date = $row['start_date'];
    $end_date = $row['end_date'];

    if ($start_date && $end_date) {

        $start = strtotime($start_date);
        $end = strtotime($end_date);
        $time = round(($end - $start));
        
        if($time>60){
            $duration = $time/60;
        } elseif($time>3600){
            $duration = $time/3600;  
        } elseif($time>86400){
            
        } else {
            $duration = $time;
        }

        
        $sql= 'UPDATE todoliste SET duration = :duration WHERE todo_id = :todo_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':todo_id', $todo_id);
        $stmt->bindParam(':duration', $duration);
        $stmt->execute();

    } else {
        
        error_log("Erreur");
    }
    
}

header('Location: index.php');
exit();
?>

