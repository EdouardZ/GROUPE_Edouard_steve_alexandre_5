<?php
$page_title = "Tableau de bord";
require_once __DIR__ . '/connexion_bd.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }

$tab = $_GET['tab'] ?? 'mes-offres';

// Mes offres
$s1 = $pdo->prepare(
    'SELECT o.*, (SELECT COUNT(*) FROM candidatures c WHERE c.id_offre = o.id) AS nb_candidats
     FROM offres o WHERE o.id_createur = ? ORDER BY o.id DESC'
);
$s1->execute([$_SESSION['user_id']]);
$mesOffres = $s1->fetchAll();

// Mes candidatures
$s2 = $pdo->prepare(
    'SELECT o.*, u.nom AS nom_createur FROM offres o
     JOIN candidatures c ON c.id_offre = o.id
     JOIN utilisateurs u ON u.id = o.id_createur
     WHERE c.id_utilisateur = ? ORDER BY o.id DESC'
);
$s2->execute([$_SESSION['user_id']]);
$mesCandidatures = $s2->fetchAll();

include 'header.php';
?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">L'offre a été supprimée avec succès.</div>
<?php endif; ?>

<div class="dashboard-header">
    <div class="dashboard-welcome">
        <div class="dashboard-avatar"><?= strtoupper(substr($_SESSION['user_nom'], 0, 1)) ?></div>
        <div>
            <h1>Bonjour, <?= htmlspecialchars($_SESSION['user_nom']) ?> 👋</h1>
            <p class="page-subtitle">Gérez vos offres et candidatures</p>
        </div>
    </div>
    <a href="creer_offre.php" class="btn btn-primary">+ Publier une offre</a>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card">
        <span class="stat-number"><?= count($mesOffres) ?></span>
        <span class="stat-label">Offre<?= count($mesOffres)>1?'s':'' ?> publiée<?= count($mesOffres)>1?'s':'' ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= array_sum(array_column($mesOffres, 'nb_candidats')) ?></span>
        <span class="stat-label">Candidature<?= array_sum(array_column($mesOffres,'nb_candidats'))>1?'s':'' ?> reçue<?= array_sum(array_column($mesOffres,'nb_candidats'))>1?'s':'' ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= count($mesCandidatures) ?></span>
        <span class="stat-label">Candidature<?= count($mesCandidatures)>1?'s':'' ?> envoyée<?= count($mesCandidatures)>1?'s':'' ?></span>
    </div>
</div>

<!-- Onglets -->
<div class="tabs">
    <a href="?tab=mes-offres"   class="tab <?= $tab==='mes-offres'   ? 'tab--active':'' ?>">Mes offres</a>
    <a href="?tab=candidatures" class="tab <?= $tab==='candidatures' ? 'tab--active':'' ?>">Mes candidatures</a>
</div>

<?php if ($tab === 'mes-offres'): ?>
    <?php if (empty($mesOffres)): ?>
        <div class="empty-state">
            <p>Vous n'avez pas encore publié d'offre.</p>
            <a href="creer_offre.php" class="btn btn-primary">Publier ma première offre</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>Titre</th><th>Type</th><th>Lieu</th><th>Salaire</th><th>Candidats</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($mesOffres as $o):
                        $slug = strtolower(str_replace(['é','è','ê','î','É'],['e','e','e','i','e'],$o['type_contrat']));
                    ?>
                        <tr>
                            <td><a href="voir_offre.php?id=<?= $o['id'] ?>"><?= htmlspecialchars($o['titre']) ?></a></td>
                            <td><span class="badge badge--<?= $slug ?>"><?= $o['type_contrat'] ?></span></td>
                            <td><?= htmlspecialchars($o['lieu'] ?? '—') ?></td>
                            <td><?= $o['salaire'] ? number_format($o['salaire'],0,',',' ').' €' : '—' ?></td>
                            <td><a href="voir_offre.php?id=<?= $o['id'] ?>" style="color:var(--purple);font-weight:700"><?= $o['nb_candidats'] ?> candidat<?= $o['nb_candidats']>1?'s':'' ?></a></td>
                            <td class="table-actions">
                                <a href="modifier_offre.php?id=<?= $o['id'] ?>" class="btn btn-ghost btn-xs">Modifier</a>
                                <a href="supprimer_offre.php?id=<?= $o['id'] ?>" class="btn btn-danger btn-xs"
                                   data-confirm="Supprimer cette offre ?" data-danger="true">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php else: ?>
    <?php if (empty($mesCandidatures)): ?>
        <div class="empty-state">
            <p>Vous n'avez pas encore postulé à des offres.</p>
            <a href="offres.php" class="btn btn-primary">Explorer les offres</a>
        </div>
    <?php else: ?>
        <div class="offres-grid">
            <?php foreach ($mesCandidatures as $o):
                $slug = strtolower(str_replace(['é','è','ê','î','É'],['e','e','e','i','e'],$o['type_contrat']));
            ?>
                <div class="offre-card offre-card--applied">
                    <div class="offre-card__header">
                        <span class="badge badge--<?= $slug ?>"><?= $o['type_contrat'] ?></span>
                        <span class="badge badge--applied">Candidaté ✓</span>
                    </div>
                    <h2 class="offre-card__titre"><a href="voir_offre.php?id=<?= $o['id'] ?>"><?= htmlspecialchars($o['titre']) ?></a></h2>
                    <p class="offre-card__entreprise"><?= htmlspecialchars($o['nom_createur']) ?></p>
                    <div class="offre-card__meta">
                        <?php if ($o['lieu']): ?><span class="meta-item">📍 <?= htmlspecialchars($o['lieu']) ?></span><?php endif; ?>
                        <?php if ($o['salaire']): ?><span class="meta-item">💶 <?= number_format($o['salaire'],0,',',' ') ?> €</span><?php endif; ?>
                    </div>
                    <div class="offre-card__actions">
                        <a href="voir_offre.php?id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">Voir l'offre</a>
                        <a href="retirer_candidature.php?id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm"
                           data-confirm="Retirer votre candidature ?" data-danger="false">Retirer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include 'footer.php'; ?>
