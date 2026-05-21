<?php
$page_title = "Accueil";
include 'header.php';
?>

<div class="page-header">
    <div>
        <h1>Bienvenue sur Baraa</h1>
        <p class="page-subtitle">Trouvez votre prochain emploi ou publiez une offre</p>
    </div>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="inscription.php" class="btn btn-primary">Créer un compte</a>
    <?php else: ?>
        <a href="creer_offre.php" class="btn btn-primary">+ Publier une offre</a>
    <?php endif; ?>
</div>

<div class="offres-grid">
    <div class="offre-card">
        <div class="offre-card__header">
            <span class="badge badge--cdi">CDI</span>
        </div>
        <h2 class="offre-card__titre"><a href="offres.php">Parcourez toutes les offres</a></h2>
        <p class="offre-card__desc">Consultez les offres disponibles et postulez en quelques clics.</p>
        <div class="offre-card__actions">
            <a href="offres.php" class="btn btn-primary btn-sm">Voir les offres</a>
        </div>
    </div>

    <?php if (!isset($_SESSION['user_id'])): ?>
    <div class="offre-card">
        <div class="offre-card__header">
            <span class="badge badge--stage">Compte</span>
        </div>
        <h2 class="offre-card__titre"><a href="inscription.php">Créez votre compte</a></h2>
        <p class="offre-card__desc">Inscrivez-vous pour postuler aux offres ou en publier.</p>
        <div class="offre-card__actions">
            <a href="inscription.php" class="btn btn-primary btn-sm">S'inscrire</a>
            <a href="connexion.php" class="btn btn-ghost btn-sm">Se connecter</a>
        </div>
    </div>
    <?php else: ?>
    <div class="offre-card">
        <div class="offre-card__header">
            <span class="badge badge--freelance">Tableau</span>
        </div>
        <h2 class="offre-card__titre"><a href="tableau_de_bord.php">Mon tableau de bord</a></h2>
        <p class="offre-card__desc">Gérez vos offres et suivez vos candidatures.</p>
        <div class="offre-card__actions">
            <a href="tableau_de_bord.php" class="btn btn-primary btn-sm">Accéder</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
