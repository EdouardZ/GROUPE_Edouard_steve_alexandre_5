<?php
require_once __DIR__ . '/connexion_bd.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: offres.php'); exit; }

$pdo->prepare('DELETE FROM candidatures WHERE id_utilisateur = ? AND id_offre = ?')
    ->execute([$_SESSION['user_id'], $id]);

header("Location: voir_offre.php?id=$id&withdrawn=1");
exit;
