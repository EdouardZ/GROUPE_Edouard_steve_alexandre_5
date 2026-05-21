<?php
require_once __DIR__ . '/connexion_bd.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: offres.php'); exit; }

// Vérifie que l'offre appartient bien à cet utilisateur
$stmt = $pdo->prepare('SELECT id FROM offres WHERE id = ? AND id_createur = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
if (!$stmt->fetch()) { header('Location: offres.php'); exit; }

// Suppression — candidatures supprimées en CASCADE
$pdo->prepare('DELETE FROM offres WHERE id = ? AND id_createur = ?')
    ->execute([$id, $_SESSION['user_id']]);

header('Location: tableau_de_bord.php?deleted=1');
exit;
