<?php
require 'bd.php';
require 'auth.php';

$user = $_SESSION['user'];

// --- Récupération des paramètres de recherche et filtrage ---
$search = $_GET['search'] ?? '';
$statut = $_GET['statut'] ?? '';

// --- Récupération des taches selon le role ---
if ($user['role'] === 'admin') {
    $sql = "SELECT tache.*, users.nom 
            FROM tache 
            JOIN users ON tache.user_id = users.id
            WHERE 1";
    $params = [];
    if ($search) {
        $sql .= " AND tache.titre LIKE ?";
        $params[] = "%$search%";
    }
    if ($statut) {
        $sql .= " AND tache.statut = ?";
        $params[] = $statut;
    }
    $sql .= " ORDER BY tache.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll();
} else {
    $sql = "SELECT * FROM tache WHERE user_id=?";
    $params = [$user['id']];
    if ($search) {
        $sql .= " AND titre LIKE ?";
        $params[] = "%$search%";
    }
    if ($statut) {
        $sql .= " AND statut = ?";
        $params[] = $statut;
    }
    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll();
}

// --- Statistiques pour tableau de bord admin ---
if ($user['role'] === 'admin') {
    $totalTasks = $pdo->query("SELECT COUNT(*) FROM tache")->fetchColumn();
    $completedTasks = $pdo->query("SELECT COUNT(*) FROM tache WHERE statut='Terminée'")->fetchColumn();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des taches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- ===== Navbar ===== -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Gestion des taches</span>
        <div class="text-white">
            <?= htmlspecialchars($user['nom']) ?> (<?= $user['role'] ?>)
            <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <!-- ===== Tableau de bord admin ===== -->
    <?php if ($user['role'] === 'admin'): ?>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total utilisateurs</h5>
                    <h3><?= $totalUsers ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Taches terminées</h5>
                    <h3><?= $completedTasks ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Total tâches</h5>
                    <h3><?= $totalTasks ?></h3>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== Formulaire ajout tache ===== -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Ajouter une tache</h5>
            <form action="action.php" method="post">
                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="En cours">En cours</option>
                        <option value="Terminée">Terminée</option>
                    </select>
                </div>

                <button class="btn btn-primary">Ajouter la tache</button>
            </form>
        </div>
    </div>

    <!-- ===== Recherche et filtrage ===== -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Recherche par titre" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="En cours" <?= $statut=='En cours'?'selected':'' ?>>En cours</option>
                        <option value="Terminée" <?= $statut=='Terminée'?'selected':'' ?>>Terminée</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-secondary w-100">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== Liste des tâches ===== -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Liste des taches</h5>

            <?php if (empty($tasks)): ?>
                <div class="alert alert-info">Aucune tache trouvée.</div>
            <?php else: ?>
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <?php if ($user['role'] === 'admin'): ?>
                                <th>Utilisateur</th>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $t): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td><?= htmlspecialchars($t['titre']) ?></td>
                                <td><?= nl2br(htmlspecialchars($t['description'])) ?></td>
                                <td>
                                    <span class="badge <?= $t['statut'] === 'Terminée' ? 'bg-success' : 'bg-warning' ?>">
                                        <?= htmlspecialchars($t['statut']) ?>
                                    </span>
                                </td>

                                <?php if ($user['role'] === 'admin'): ?>
                                    <td><?= htmlspecialchars($t['nom']) ?></td>
                                <?php endif; ?>

                                <td>
                                    <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <a href="action.php?delete=<?= $t['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Supprimer cette tache ?')">
                                       Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>
