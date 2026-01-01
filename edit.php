<?php
require 'bd.php';
require 'auth.php';

$user = $_SESSION['user'];

// Vérification de l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Récupération de la tâche selon le rôle
if ($user['role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM tache WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare(
        "SELECT * FROM tache WHERE id = ? AND user_id = ?"
    );
    $stmt->execute([$id, $user['id']]);
}

$tache = $stmt->fetch();

if (!$tache) {
    header("Location: index.php");
    exit;
}

// Mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $statut = $_POST['statut'];

    if ($user['role'] === 'admin') {
        $stmt = $pdo->prepare(
            "UPDATE tache SET titre=?, description=?, statut=? WHERE id=?"
        );
        $stmt->execute([$titre, $description, $statut, $id]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE tache SET titre=?, description=?, statut=? WHERE id=? AND user_id=?"
        );
        $stmt->execute([$titre, $description, $statut, $id, $user['id']]);
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une tache</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Modification de tache</span>
        <a href="index.php" class="btn btn-outline-light btn-sm">Retour</a>
    </div>
</nav>

<div class="container mt-5" style="max-width: 600px;">

    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="card-title mb-4 text-center">Modifier la tache</h4>

            <form method="post">

                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required
                           value="<?= htmlspecialchars($tache['titre']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($tache['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="En cours" <?= $tache['statut'] === 'En cours' ? 'selected' : '' ?>>
                            En cours
                        </option>
                        <option value="Terminée" <?= $tache['statut'] === 'Terminée' ? 'selected' : '' ?>>
                            Terminée
                        </option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary">Mettre a jour</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
