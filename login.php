<?php

/**
 * Page de connexion
 * - Vérification via password_verify
 * - Protection CSRF
 * - Limitation basique des tentatives (anti brute-force simple, en session)
 */

require_once 'cnx.php';
require_once 'csrf.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];

// Anti brute-force basique
if (empty($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_lock_until'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (time() < $_SESSION['login_lock_until']) {
        $wait = $_SESSION['login_lock_until'] - time();
        $errors[] = "Trop de tentatives. Réessayez dans {$wait} secondes.";
    } elseif (!csrf_verify()) {
        $errors[] = "Requête invalide, veuillez réessayer.";
    } else {
        $identifier = trim($_POST['identifier'] ?? ''); // username ou email
        $password   = $_POST['password'] ?? '';

        if ($identifier === '' || $password === '') {
            $errors[] = "Veuillez remplir tous les champs.";
        } else {
            $stmt = $pdo->prepare('SELECT user_id, username, password FROM users WHERE username = :id OR email = :id');
            $stmt->bindParam(':id', $identifier);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['last_regen'] = time();
                $_SESSION['login_attempts'] = 0;

                // Réhachage si l'algorithme par défaut a changé entre-temps
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare('UPDATE users SET password = :password WHERE user_id = :id');
                    $upd->bindParam(':password', $newHash);
                    $upd->bindParam(':id', $user['user_id']);
                    $upd->execute();
                }

                header('Location: index.php');
                exit();
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_lock_until'] = time() + 60; // verrouillage 60s
                    $_SESSION['login_attempts'] = 0;
                }
                // Message volontairement générique : ne pas révéler si c'est le login ou le mdp qui est faux
                $errors[] = "Identifiants incorrects.";
            }
        }
    }
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
    <title>Connexion — Todo List</title>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        .authInput {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 15px;
            color: #1e293b;
            background: white;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .authInput:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .authSubmit {
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
        .authSubmit:hover { background: #4f46e5; }
        .authSubmit:active { transform: scale(0.98); }
        .authLink { color: #6366f1; text-decoration: none; font-weight: 500; }
        .authLink:hover { text-decoration: underline; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4" style="background-color:#0f0f1a; background-image: radial-gradient(circle, #3a3a6a 1px, transparent 1px); background-size: 20px 20px;">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl border border-slate-100 p-8" style="box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

            <div class="mb-8">
                <p class="font-display text-2xl font-bold text-slate-800">Connexion</p>
                <p class="text-slate-400 text-sm mt-1">Accédez à votre liste de tâches.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 p-4">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="flex flex-col gap-4" autocomplete="off">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1">NOM D'UTILISATEUR OU EMAIL</label>
                    <input class="authInput" type="text" name="identifier" required
                           value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1">MOT DE PASSE</label>
                    <input class="authInput" type="password" name="password" required>
                </div>

                <input class="authSubmit" type="submit" value="Se connecter">
            </form>

            <p class="text-sm text-slate-400 mt-6 text-center">
                Pas encore de compte ? <a href="register.php" class="authLink">Inscrivez-vous</a>
            </p>
        </div>
    </div>

</body>
</html>
