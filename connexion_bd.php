<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// connexion_bd.php — Connexion PDO à la base de données
// A inclure en haut de chaque page qui utilise la BDD

$host   = 'localhost';
$dbname = 'pro_offre';    // nom de la base dans phpMyAdmin
$user   = 'root';
$pass   = '';             // vide par défaut sur WAMP/XAMPP

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('<p style="color:red;font-family:sans-serif;padding:1em;background:#FEE2E2;border-radius:8px;margin:1em">
        <strong>Erreur BDD :</strong> ' . $e->getMessage() . '<br>
        Vérifiez que WAMP est démarré et que la base "pro_offre" existe dans phpMyAdmin.
    </p>');
}
