
<?php 

/**
 * Suppression d'une tâche à partir de son id
 */

require_once 'cnx.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todo_id = $_POST['todo_id'];

  

    $sql = 'DELETE FROM todoliste WHERE todo_id = :todo_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->execute();
}

header('Location: index.php');
exit();
?>
