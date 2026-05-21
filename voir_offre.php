<?php
require_once __DIR__ . '/connexion_bd.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: offres.php'); exit; }

$stmt = $pdo->prepare(
    'SELECT o.*, u.nom AS nom_createur FROM offres o
     JOIN utilisateurs u ON u.id = o.id_createur WHERE o.id = ?'
);
$stmt->execute([$id]);
$offre = $stmt->fetch();
if (!$offre) { header('Location: offres.php'); exit; }

$page_title = $offre['titre'];
$isOwner    = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $offre['id_createur'];

// L'utilisateur a-t-il postulé ?
$applied = false;
if (isset($_SESSION['user_id']) && !$isOwner) {
    $s = $pdo->prepare('SELECT 1 FROM candidatures WHERE id_utilisateur = ? AND id_offre = ?');
    $s->execute([$_SESSION['user_id'], $id]);
    $applied = (bool)$s->fetch();
}

// Liste des candidats (créateur uniquement)
$candidats = [];
if ($isOwner) {
    $s = $pdo->prepare(
        'SELECT u.nom, u.email, u.cv_texte FROM utilisateurs u
         JOIN candidatures c ON c.id_utilisateur = u.id
         WHERE c.id_offre = ? ORDER BY u.nom'
    );
    $s->execute([$id]);
    $candidats = $s->fetchAll();
}

$slug = strtolower(str_replace(['é','è','ê','î','É'], ['e','e','e','i','e'], $offre['type_contrat']));

include 'header.php';
?>

<a href="offres.php" class="back-link">← Toutes les offres</a>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success" style="margin-top:1rem">Offre mise à jour avec succès.</div>
<?php endif; ?>

<div class="offre-detail-header">
    <div class="offre-detail-meta">
        <span class="badge badge--<?= $slug ?>"><?= htmlspecialchars($offre['type_contrat']) ?></span>
        <?php if ($offre['lieu']): ?><span class="meta-item">📍 <?= htmlspecialchars($offre['lieu']) ?></span><?php endif; ?>
        <?php if ($offre['salaire']): ?><span class="meta-item">💶 <?= number_format($offre['salaire'],0,',',' ') ?> €/mois</span><?php endif; ?>
    </div>
    <h1><?= htmlspecialchars($offre['titre']) ?></h1>
    <p class="offre-detail-entreprise">Publié par <strong><?= htmlspecialchars($offre['nom_createur']) ?></strong></p>
</div>

<div class="offre-detail-body">
    <!-- Description -->
    <div class="offre-detail-desc">
        <h2>Description du poste</h2>
        <div class="prose"><?= nl2br(htmlspecialchars($offre['description'])) ?></div>
    </div>

    <!-- Sidebar actions -->
    <div>
        <div class="sidebar-card">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <p class="sidebar-hint">Connectez-vous pour postuler.</p>
                <a href="connexion.php" class="btn btn-primary btn-full">Se connecter</a>

            <?php elseif ($isOwner): ?>
                <p class="sidebar-hint">Vous avez créé cette offre.</p>
                <a href="modifier_offre.php?id=<?= $id ?>" class="btn btn-secondary btn-full">Modifier</a>
                <a href="supprimer_offre.php?id=<?= $id ?>" class="btn btn-danger btn-full"
                   data-confirm="Supprimer définitivement cette offre ?" data-danger="true">Supprimer</a>

            <?php elseif (!$applied): ?>
                <p class="sidebar-hint">Ce poste vous intéresse ?</p>
                <a href="postuler.php?id=<?= $id ?>" class="btn btn-primary btn-full">Postuler maintenant</a>

            <?php else: ?>
                <div class="applied-badge">✓ Vous avez postulé</div>
                <a href="retirer_candidature.php?id=<?= $id ?>" class="btn btn-ghost btn-full"
                   data-confirm="Retirer votre candidature ?" data-danger="false">Retirer ma candidature</a>
            <?php endif; ?>
        </div>

        <!-- Candidats — créateur seulement -->
        <?php if ($isOwner): ?>
            <div class="sidebar-card">
                <h3>Candidats <span style="color:var(--purple)">(<?= count($candidats) ?>)</span></h3>
                <?php if (empty($candidats)): ?>
                    <p class="sidebar-hint">Aucun candidat pour le moment.</p>
                <?php else: ?>
                    <ul class="candidats-list">
                        <?php foreach ($candidats as $c): ?>
                            <li class="candidat-item">
                                <div class="candidat-avatar"><?= strtoupper(substr($c['nom'],0,1)) ?></div>
                                <div class="candidat-info">
                                    <strong><?= htmlspecialchars($c['nom']) ?></strong>
                                    <a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="candidat-email">
                                        <?= htmlspecialchars($c['email']) ?>
                                    </a>
                                    <?php if ($c['cv_texte']): ?>
                                        <details class="candidat-cv">
                                            <summary>Voir le CV</summary>
                                            <p><?= nl2br(htmlspecialchars($c['cv_texte'])) ?></p>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
