<?php
/**
 * Fichier de connexion à la base de données
 * Utilise PDO pour la sécurité contre les injections SQL
 */

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=gestion_taches;charset=utf8",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Démarrage de la session
session_start();
?>