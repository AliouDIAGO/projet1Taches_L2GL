<?php
require 'bd.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Vérifier si l'email existe déjà
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "Cet email est déjà utilisé.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $nom,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role
        ]);

        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-body">

            <h4 class="text-center mb-4">Créer un compte</h4>

            <?php if ($message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post">

                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user">Utilisateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>

                <button class="btn btn-success w-100">
                    S'inscrire
                </button>

            </form>

            <hr>

            <p class="text-center mb-0">
                Vous avez déja un compte ?
                <a href="login.php">Se connecter</a>
            </p>

        </div>
    </div>
</div>

</body>
</html>
