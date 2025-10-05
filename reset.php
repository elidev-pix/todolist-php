<?php 

/**
 * Réinitialisation de la page 
 * On garde le même id
 * start_date,end_date,duration sont supprimé - SET NULL
 * todo_reset est initalisé à 1, cette valeur servira de repère pour indiquer la réinitialisation
 */
require_once 'cnx.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todo_id = $_POST['todo_id'];
    $end_date = $_POST['end_date'];

   $sql= 'UPDATE todoliste SET todo_reset = 1,start_date = NULL, end_date= NULL,start_time = NULL,end_time = NULL,start = NULL,end = NULL,duration=NULL WHERE todo_id = :todo_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->execute();

}

header('Location: index.php');
exit();
?>

