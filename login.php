<?php
require 'bd.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $message = "Email ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 420px;">
    <div class="card shadow">
        <div class="card-body">

            <h4 class="text-center mb-4">Connexion</h4>

            <?php if ($message): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Se connecter</button>
            </form>

            <hr>

            <p class="text-center mb-0">
                Vous n’avez pas de compte ?
                <a href="register.php">S’inscrire</a>
            </p>

        </div>
    </div>
</div>

</body>
</html>
