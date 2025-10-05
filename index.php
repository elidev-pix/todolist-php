<?php 

/**
 * Page Principale contenant les titres, l'input de saisie et les formulaires
 * Utlisation d'une boucle while pour l'affichage des tâches
 * Utilisation de la syntaxe  ORDER BY todo_id DESC pour affichage des tâches les plus récentes en premier
 */
require 'cnx.php';

$sql = 'SELECT * FROM todoliste ORDER BY todo_id DESC';
$resultat = $pdo->prepare($sql);
$resultat->execute();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Todo List</title>
</head>
<body>
    <div class="container">
        <p id="first">Todo List</p>
        <p id="second">Ajouter une nouvelle tâche</p>
        <form action="add.php" method="POST">
            <input type="text" name="todo" id="todo" required> <br><br>
            <button class="save" type="submit">Enregistrer</button>
        </form>
        <div id="list">

            <?php while($data = $resultat->fetch()) { ?>
                <div class="todo-item ">
                    <div class="top">
                        <ion-icon class="color <?= $data['end_date'] ? 'done' : ($data['start_date'] ? 'while' : '') ?><?= $data['todo_reset'] ? 'reloaded': '' ?>" name="ellipse-outline"></ion-icon>

                        <span class="todo-name <?= $data['end_date'] ? 'todo-valide' : '' ?><?= $data['todo_reset'] ? 'reloaded': '' ?>"><?= htmlspecialchars($data['todo_name']); ?>
                        </span>

                        <a href="update.php?id=<?= urlencode($data['todo_id']); ?>">
                            <button>
                                <ion-icon name="pencil-outline" class="pencilIcon <?= $data['start_date'] ? 'penciled': '' ?><?= $data['todo_reset'] ? 'reloaded': '' ?>"></ion-icon>
                            </button>
                        </a>

                        <form action="delete.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                            <button class="delete" type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                                <ion-icon name="trash-bin-outline" class="deleteIcon  <?= $data['start_date'] ? 'deleted': '' ?><?= $data['todo_reset'] ? 'reloaded': '' ?><?= $data['end_date'] ? 'reseted': '' ?>"></ion-icon>
                            </button>
                        </form>

                        <form action="start.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                            <button type="submit">
                                <ion-icon class="startIcon <?= $data['start_date'] ? 'started': '' ?><?= $data['todo_reset'] ? 'reloaded': '' ?>" name="add-circle-outline"></ion-icon>
                            </button>
                        </form>

                        <form class="endForm" action="end.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                            <button type="submit">
                                <ion-icon class="endIcon <?= $data['start_date'] ? 'checked': '' ?> <?= $data['end_date'] ? 'finished': '' ?>" name="checkmark-done-circle-outline"></ion-icon>
                            </button>
                        </form>

                        <form class="resetForm" action="reset.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                            <button type="submit">
                                <ion-icon class="resetIcon <?= $data['todo_reset'] ? 'reloaded': '' ?><?= $data['end_date'] ? 'reseted': '' ?>" name="refresh-circle-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                    <div class="bottom">

                        <div class="start"> 
                            <p>Date de début</p>
                            <small class="date <?= $data['start'] ? 'checked': '' ?>"><?= (new DateTime($data['start_date']))->format('d/m/Y'); ?><?= '  à  '.  $data['start_time']; ?></small>
                            <div class="line <?= $data['start'] ? 'checked': '' ?>">---</div>
                            
                        </div> 
                        
                        <div>

                            <p>Durée</p>
                            <?= $data['duration']; ?>
                            <div class="line <?= $data['duration'] ? 'checked': '' ?>">---</div>
                        </div> 
                            
                        <div class="end">
                            <p>Date de fin</p>
                            <small  class="date <?= $data['end'] ? 'checked': '' ?>"><?= (new DateTime($data['end_date']))->format('d/m/Y'); ?><?= '  à  '.$data['end_time']; ?></small>
                            <div class="line <?= $data['end'] ? 'checked': '' ?>">---</div>
                        </div>

                    </div>
                    

                </div>
                
            
            <?php } ?>
        </div>
    </div>
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>


<form action="duration.php" method="post" style="margin: 0;">
    <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>"></input> 
</form>