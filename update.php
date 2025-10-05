<?php

/**
 * Modification d'une tâche à partir de son id (s'affiche dans l'url)
 * Dirigé vers une page avec input type text contenant le texte de la tâche à modifier
 * Et input type submit pour enregistrer dans la BDD
 */
require_once 'cnx.php';

if (isset($_GET['id'])) {
    $todo_id = $_GET['id'];

    $sql = 'SELECT * FROM todoliste WHERE todo_id = :todo_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':todo_id', $todo_id);
    $stmt->execute();
    $data = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $todo_name = $_POST['todo'];

        $sql = 'UPDATE todoliste SET todo_name = :todo_name WHERE todo_id = :todo_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':todo_name', $todo_name);
        $stmt->bindParam(':todo_id', $todo_id);
        $stmt->execute();

        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Update task</title>
</head>
<body>
    <form action="" method="POST">
        <input class="updateInput" name="todo" value="<?= htmlspecialchars($data['todo_name']); ?>" required> <br>
        <input class="updateSubmitInput" type="submit" value="Modifier la tâche">
    </form>
</body>
</html>

