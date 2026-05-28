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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Modifier la tâche</title>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        .updateInput {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: #1e293b;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .updateInput:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }

        .updateSubmitInput {
            width: 100%;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }
        .updateSubmitInput:hover { background: #4f46e5; }
        .updateSubmitInput:active { transform: scale(0.98); }

        .back-link {
            color: #94a3b8;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.15s;
        }
        .back-link:hover { color: #6366f1; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card { animation: fadeUp 0.3s ease both; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4" style="background-color:#0f0f1a; background-image: radial-gradient(circle, #3a3a6a 1px, transparent 1px); background-size: 20px 20px;">

    <div class="card w-full max-w-md">

        <!-- Retour -->
        <div class="mb-6">
            <a href="index.php" class="back-link">
                <ion-icon name="arrow-back-outline" style="font-size:15px; position:static; transform:none;"></ion-icon>
                Retour à la liste
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl border border-slate-100 p-8" style="box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

            <!-- En-tête -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <ion-icon name="pencil-outline" style="font-size:16px; color:#6366f1; position:static; transform:none;"></ion-icon>
                    </div>
                    <p class="font-display text-xl font-bold text-slate-800">Modifier la tâche</p>
                </div>
                <p class="text-slate-400 text-sm pl-11">Éditez le nom puis enregistrez.</p>
            </div>

            <!-- Formulaire -->
            <form action="" method="POST" class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1" style="font-family:'Syne',sans-serif; letter-spacing:0.04em;">
                        NOM DE LA TÂCHE
                    </label>
                    <input
                        class="updateInput"
                        name="todo"
                        value="<?= htmlspecialchars($data['todo_name']); ?>"
                        required
                    >
                </div>
                <input class="updateSubmitInput" type="submit" value="Enregistrer les modifications">
            </form>

        </div>

    </div>

</body>
</html>