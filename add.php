
<?php 

/**
 * Ajout de nouvelle tâche
 */

require_once 'cnx.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $todo = $_POST['todo'];
    $sql = 'INSERT INTO todoliste (todo_name) VALUES (:todo)';             
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo', $todo);
    $stmt->execute();
   

header('Location:index.php');
exit;


}

?>

