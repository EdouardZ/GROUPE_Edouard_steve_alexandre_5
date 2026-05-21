<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . 'Baraa' : "baraa Offres d'emploi"; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="menu">
            <ul>
                <li><a href="page1.php" class="nav-brand" style="color:#0A5DC2 !important; font-style:italic;">Baraa</a></li>
                <li><a href="offres.php">Offres d'emploi</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="creer_offre.php">Publier</a></li>
                    <li><a href="tableau_de_bord.php">Tableau de bord</a></li>
                    <li class="nav-user-info">
                        <span class="nav-avatar"><?php echo strtoupper(substr($_SESSION['user_nom'], 0, 1)); ?></span>
                        <span class="nav-username"><?php echo htmlspecialchars($_SESSION['user_nom']); ?></span>
                        <a href="deconnexion.php" class="btn-deconnexion">Déconnexion</a>
                    </li>
                <?php else: ?>
                    <li><a href="connexion.php" class="btn-nav-ghost">Connexion</a></li>
                    <li><a href="inscription.php" class="btn-nav-primary">S'inscrire</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
