<?php 

/**
 * Ajout de la date de début de la tâche au moment du clique sur l'icone en forme de +
 */


require_once 'cnx.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $todo_id = $_POST['todo_id'];
    $now = new DateTime();
    $start_date = $now->format('Y/m/d');
    $start_time = $now->format('H:i:s');
    $start = $now->format('Y/m/d H:i:s');
    
    
$sql= 'UPDATE todoliste SET start_date = :start_date,start_time = :start_time,start = :start,todo_reset=0 WHERE todo_id = :todo_id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':todo_id',$todo_id);
$stmt->bindParam(':start_date',$start_date);
$stmt->bindParam(':start_time',$start_time);
$stmt->bindParam(':start',$start);
$stmt->execute();

header('Location:index.php');
exit();


}

?>