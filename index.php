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
    <title>Todo List</title>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        h1, h2, .font-display { font-family: 'Syne', sans-serif; }

        .todo-card {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .todo-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        /* Voyant d'état */
        .voyant {
            font-size: 10px !important;
            --ionicon-stroke-width: 60px;
            transition: color 0.3s, background-color 0.3s;
            /* couleur par défaut : bleu/listed */
            color: #7dd3fc;
            background-color: #7dd3fc;
            border-radius: 50%;
        }
        .voyant.while { color: #f87171; background-color: #f87171; }
        .voyant.done  { color: #4ade80; background-color: #4ade80; }
        .voyant.reloaded { color: #7dd3fc; background-color: #7dd3fc; }

        /* Icônes d'action */
        ion-icon.action {
            font-size: 18px;
            cursor: pointer;
            transition: transform 0.15s, color 0.15s;
        }
        ion-icon.action:hover { transform: scale(1.2); }

        /* Pencil */
        .pencilIcon { color: #94a3b8; }
        .pencilIcon:hover { color: #6366f1 !important; }
        .pencilIcon.started { display: none; }

        /* Start */
        .startIcon { color: #94a3b8; }
        .startIcon:hover { color: #f59e0b !important; }
        .startIcon.started { display: none; }
        .startIcon.reseted { display: inline; }

        /* End */
        .endIcon { display: none; color: #94a3b8; }
        .endIcon:hover { color: #22c55e !important; }
        .endIcon.checked { display: inline; }
        .endIcon.finished { display: none; }

        /* Reset */
        .resetIcon { display: none; color: #94a3b8; }
        .resetIcon:hover { color: #60a5fa !important; }
        .resetIcon.reseted { display: inline; }
        .resetIcon.ended { display: inline; }

        /* Delete */
        .deleteIcon { color: #94a3b8; }
        .deleteIcon:hover { color: #ef4444 !important; }
        .deleteIcon.started { display: none; }
        .deleteIcon.reseted { display: inline; }
        .deleteIcon.ended { display: inline; }

        /* Nom de la tâche */
        .todo-valide { text-decoration: line-through; color: #94a3b8; }
        .todo-name.reloaded { text-decoration: none; color: inherit; }

        /* Dates */
        .date { display: none; }
        .date.checked { display: inline; }
        .line { display: inline; color: #cbd5e1; font-size: 11px; }
        .line.checked { display: none; }

        /* Barre de progression */
        .progress-bar { height: 2px; background: #e2e8f0; border-radius: 1px; }
        .progress-fill { height: 100%; border-radius: 1px; transition: width 0.4s; }

        /* Input focus */
        input[type="text"]:focus { outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }

        /* Bouton Enregistrer */
        .save {
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 24px;
            font-family: 'Syne', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            white-space: nowrap;
        }
        .save:hover { background: #4f46e5; }
        .save:active { transform: scale(0.97); }

        button { cursor: pointer; }

        /* Animation d'entrée des cartes */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .todo-card { animation: fadeUp 0.3s ease both; }
    </style>
</head>
<body class="min-h-screen" style="background-color:#0f0f1a; background-image: radial-gradient(circle, #3a3a6a 1px, transparent 1px); background-size: 20px 20px;">

    <div class="max-w-2xl mx-auto px-4 py-12">

        <!-- En-tête -->
        <div class="mb-10">
            <p id="first" class="font-display text-4xl text-white font-bold tracking-tight">Todo List</p>
            <p id="second" class="text-slate-400 text-white mt-1 text-sm">Organisez vos tâches simplement</p>
        </div>

        <!-- Formulaire d'ajout -->
        <form action="add.php" method="POST" class="flex gap-3 mb-10">
            <input
                type="text"
                name="todo"
                id="todo"
                required
                placeholder="Nouvelle tâche…"
                class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white text-slate-800 placeholder-slate-400 transition"
                style="font-family:'DM Sans',sans-serif;"
            >
            <button class="save" type="submit">
                + Enregistrer
            </button>
        </form>

        <!-- Liste des tâches -->
        <div id="list" class="flex flex-col gap-4">

            <?php
            $delay = 0;
            while($data = $resultat->fetch()) {
                $isStarted = !empty($data['start_date']);
                $isEnded   = !empty($data['end_date']);
                $isReset   = !empty($data['todo_reset']);

                // Classes voyant
                $voyantClass = '';
                if ($isEnded)   $voyantClass = 'done';
                elseif ($isStarted) $voyantClass = 'while';
                if ($isReset)   $voyantClass = 'reloaded';

                // Badge statut
                $badgeText  = 'À faire';
                $badgeBg    = 'bg-slate-100 text-slate-500';
                if ($isStarted && !$isEnded) { $badgeText = 'En cours'; $badgeBg = 'bg-amber-50 text-amber-600'; }
                if ($isEnded)                { $badgeText = 'Terminé';  $badgeBg = 'bg-green-50 text-green-600'; }
                if ($isReset)                { $badgeText = 'À faire';  $badgeBg = 'bg-slate-100 text-slate-500'; }

                // Progression
                $progress = 0;
                if ($isStarted && !$isEnded) $progress = 50;
                if ($isEnded) $progress = 100;
                if ($isReset) $progress = 0;
                $progressColor = $isEnded ? '#4ade80' : ($isStarted ? '#f59e0b' : '#e2e8f0');

                // Dates formatées
                $startDateStr = '';
                $endDateStr   = '';
                if ($isStarted) {
                    $startDateStr = (new DateTime($data['start_date']))->format('d/m/Y') . '  à  ' . $data['start_time'];
                }
                if ($isEnded) {
                    $endDateStr = (new DateTime($data['end_date']))->format('d/m/Y') . '  à  ' . $data['end_time'];
                }
            ?>

            <div class="todo-card bg-white rounded-2xl border border-slate-100 overflow-hidden"
                 style="animation-delay: <?= $delay ?>ms; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">

                <!-- Barre de progression -->
                <div class="progress-bar">
                    <div class="progress-fill" style="width:<?= $progress ?>%; background:<?= $progressColor ?>;"></div>
                </div>

                <!-- Ligne principale -->
                <div class="top flex items-center gap-3 px-5 py-4">

                    <!-- Voyant -->
                    <ion-icon
                        class="voyant <?= $voyantClass ?>"
                        name="ellipse"
                        style="position:static; transform:none; flex-shrink:0;"
                    ></ion-icon>

                    <!-- Nom de la tâche -->
                    <span class="todo-name flex-1 text-slate-800 text-sm font-medium <?= $isEnded ? 'todo-valide' : '' ?> <?= $isReset ? 'reloaded' : '' ?>">
                        <?= htmlspecialchars($data['todo_name']); ?>
                    </span>

                    <!-- Badge statut -->
                    <span class="text-xs font-medium px-3 py-1 rounded-full <?= $badgeBg ?>" style="font-family:'Syne',sans-serif; flex-shrink:0;">
                        <?= $badgeText ?>
                    </span>

                    <!-- Actions -->

                    <!-- Modifier -->
                    <a href="update.php?id=<?= urlencode($data['todo_id']); ?>">
                        <button title="Modifier" style="background:none;border:none;display:flex;align-items:center;">
                            <ion-icon
                                name="pencil-outline"
                                class="action pencilIcon <?= $isStarted ? 'started' : '' ?> <?= $isReset ? 'reloaded' : '' ?>"
                                style="position:static;transform:none;"
                            ></ion-icon>
                        </button>
                    </a>

                    <!-- Supprimer -->
                    <form action="delete.php" method="POST" style="margin:0;display:flex;align-items:center;">
                        <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                        <button class="delete" type="submit" onclick="return confirm('Supprimer cette tâche ?')" title="Supprimer" style="background:none;border:none;display:flex;align-items:center;">
                            <ion-icon
                                name="trash-bin-outline"
                                class="action deleteIcon <?= $isStarted ? 'started' : '' ?> <?= $isReset ? 'reseted' : '' ?> <?= $isEnded ? 'ended' : '' ?>"
                                style="position:static;transform:none;"
                            ></ion-icon>
                        </button>
                    </form>

                    <!-- Démarrer -->
                    <form action="start.php" method="POST" style="margin:0;display:flex;align-items:center;">
                        <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                        <button type="submit" title="Démarrer" style="background:none;border:none;display:flex;align-items:center;">
                            <ion-icon
                                name="add-circle-outline"
                                class="action startIcon <?= $isStarted ? 'started' : '' ?> <?= $isReset ? 'reloaded' : '' ?>"
                                style="position:static;transform:none;"
                            ></ion-icon>
                        </button>
                    </form>

                    <!-- Terminer -->
                    <form class="endForm" action="end.php" method="POST" style="margin:0;display:flex;align-items:center;">
                        <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                        <button type="submit" title="Terminer" style="background:none;border:none;display:flex;align-items:center;">
                            <ion-icon
                                name="checkmark-done-circle-outline"
                                class="action endIcon <?= $isStarted ? 'checked' : '' ?> <?= $isEnded ? 'finished' : '' ?>"
                                style="position:static;transform:none;"
                            ></ion-icon>
                        </button>
                    </form>

                    <!-- Réinitialiser -->
                    <form class="resetForm" action="reset.php" method="POST" style="margin:0;display:flex;align-items:center;">
                        <input type="hidden" name="todo_id" value="<?= $data['todo_id']; ?>">
                        <button type="submit" title="Réinitialiser" style="background:none;border:none;display:flex;align-items:center;">
                            <ion-icon
                                name="refresh-circle-outline"
                                class="action resetIcon <?= $isReset ? 'reloaded' : '' ?> <?= $isEnded ? 'ended' : '' ?>"
                                style="position:static;transform:none;"
                            ></ion-icon>
                        </button>
                    </form>

                </div>

                <!-- Ligne métadonnées -->
                <div class="bottom flex items-center gap-6 px-5 pb-4 text-xs text-slate-400" style="border-top: 1px solid #f1f5f9;">

                    <!-- Date de début -->
                    <div class="start flex items-center gap-1 pt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <small class="date <?= $isStarted ? 'checked' : '' ?>"><?= $startDateStr ?></small>
                        <span class="line <?= $isStarted ? 'checked' : '' ?>">Début —</span>
                    </div>

                    <!-- Durée -->
                    <div class="flex items-center gap-1 pt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?php if (!empty($data['duration'])): ?>
                            <span class="text-slate-600 font-medium"><?= $data['duration']; ?></span>
                        <?php else: ?>
                            <span class="line">Durée —</span>
                        <?php endif; ?>
                    </div>

                    <!-- Date de fin -->
                    <div class="end flex items-center gap-1 pt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <small class="date <?= $isEnded ? 'checked' : '' ?>"><?= $endDateStr ?></small>
                        <span class="line <?= $isEnded ? 'checked' : '' ?>">Fin —</span>
                    </div>

                </div>
            </div>

            <?php
                $delay += 50;
            } ?>

        </div>

        <!-- Pied de page -->
        <div class="mt-8 text-center text-xs text-slate-400" style="font-family:'DM Sans',sans-serif;">
            Todo List &mdash; organisé avec soin
        </div>

    </div>

    <script src="script.js"></script>
</body>
</html>