<?php
require_once __DIR__ . '/connexion_bd.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: offres.php'); exit; }

// Vérifier que l'offre existe et que ce n'est pas la sienne
$stmt = $pdo->prepare('SELECT id_createur FROM offres WHERE id = ?');
$stmt->execute([$id]);
$offre = $stmt->fetch();
if (!$offre || $offre['id_createur'] == $_SESSION['user_id']) {
    header("Location: voir_offre.php?id=$id"); exit;
}

// INSERT IGNORE évite les doublons sans erreur
$pdo->prepare('INSERT IGNORE INTO candidatures (id_utilisateur, id_offre) VALUES (?, ?)')
    ->execute([$_SESSION['user_id'], $id]);

header("Location: voir_offre.php?id=$id&applied=1");
exit;
