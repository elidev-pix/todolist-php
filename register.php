<?php

/**
 * Page d'inscription
 * - Validation des champs
 * - Mot de passe haché avec password_hash (bcrypt)
 * - Vérification d'unicité username / email
 * - Protection CSRF
 */

require_once 'cnx.php';
require_once 'csrf.php';

// Si déjà connecté, on redirige vers l'accueil
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        $errors[] = "Requête invalide, veuillez réessayer.";
    }

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validations
    if ($username === '' || strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Le nom d'utilisateur doit contenir entre 3 et 50 caractères.";
    }
    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $username)) {
        $errors[] = "Le nom d'utilisateur ne peut contenir que lettres, chiffres, - et _.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        // Vérifie l'unicité
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE username = :username OR email = :email');
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->fetch()) {
            $errors[] = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hash);
            $stmt->execute();

            $userId = $pdo->lastInsertId();

            // Connexion automatique après inscription
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['last_regen'] = time();

            header('Location: index.php');
            exit();
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
    <title>Inscription — Todo List</title>
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
                <p class="font-display text-2xl font-bold text-slate-800">Créer un compte</p>
                <p class="text-slate-400 text-sm mt-1">Inscrivez-vous pour gérer vos tâches.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 p-4">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="flex flex-col gap-4" autocomplete="off">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1">NOM D'UTILISATEUR</label>
                    <input class="authInput" type="text" name="username" required minlength="3" maxlength="50"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1">EMAIL</label>
                    <input class="authInput" type="email" name="email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1">MOT DE PASSE</label>
                    <input class="authInput" type="password" name="password" required minlength="8">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-2 pl-1">CONFIRMER LE MOT DE PASSE</label>
                    <input class="authInput" type="password" name="password_confirm" required minlength="8">
                </div>

                <input class="authSubmit" type="submit" value="S'inscrire">
            </form>

            <p class="text-sm text-slate-400 mt-6 text-center">
                Déjà un compte ? <a href="login.php" class="authLink">Connectez-vous</a>
            </p>
        </div>
    </div>

</body>
</html>
