<?php
require 'bd.php';
require 'auth.php';

$user = $_SESSION['user'];

if (isset($_POST['titre'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO tache (titre, description, statut, user_id) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['titre'],
        $_POST['description'],
        $_POST['statut'],
        $user['id']
    ]);
}

if (isset($_GET['delete'])) {
    if ($user['role'] === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM tache WHERE id=?");
        $stmt->execute([$_GET['delete']]);
    } else {
        $stmt = $pdo->prepare(
            "DELETE FROM tache WHERE id=? AND user_id=?"
        );
        $stmt->execute([$_GET['delete'], $user['id']]);
    }
}

header("Location: index.php");
