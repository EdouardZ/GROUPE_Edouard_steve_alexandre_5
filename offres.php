<?php
$page_title = "Offres d'emploi";
require_once __DIR__ . '/connexion_bd.php';

// Filtres & tri,  récupèrent ce que l'utilisateur a tapé dans les filtres.
$lieu         = trim($_GET['lieu']         ?? '');
$type_contrat = trim($_GET['type_contrat'] ?? '');
$tri          =      $_GET['tri']          ?? '';

$params = []; $where = [];
if ($lieu !== '')         { $where[] = 'o.lieu LIKE ?';        $params[] = "%$lieu%"; }
if ($type_contrat !== '') { $where[] = 'o.type_contrat = ?';   $params[] = $type_contrat; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$orderSQL = 'ORDER BY o.id DESC';
if ($tri === 'salaire_asc')  $orderSQL = 'ORDER BY o.salaire ASC';
if ($tri === 'salaire_desc') $orderSQL = 'ORDER BY o.salaire DESC';

$stmt = $pdo->prepare(
    "SELECT o.*, u.nom AS nom_createur,
     (SELECT COUNT(*) FROM candidatures c WHERE c.id_offre = o.id) AS nb_candidats
     FROM offres o JOIN utilisateurs u ON u.id = o.id_createur
     $whereSQL $orderSQL"
);
$stmt->execute($params);
$offres = $stmt->fetchAll();

// Candidatures de l'utilisateur connecté
$mesCandidatures = [];
if (isset($_SESSION['user_id'])) {
    $s = $pdo->prepare('SELECT id_offre FROM candidatures WHERE id_utilisateur = ?');
    $s->execute([$_SESSION['user_id']]);
    foreach ($s->fetchAll() as $r) $mesCandidatures[$r['id_offre']] = true;
}

$typesContrat = ['CDI', 'CDD', 'Freelance', 'Stage', 'Intérim'];

include 'header.php';
?>

<div class="page-header">
    <div>
        <h1>Offres d'emploi</h1>
        <p class="page-subtitle"><?= count($offres) ?> offre<?= count($offres) > 1 ? 's' : '' ?> disponible<?= count($offres) > 1 ? 's' : '' ?></p>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="creer_offre.php" class="btn btn-primary">+ Publier une offre</a>
    <?php endif; ?>
</div>

<!-- Filtres BONUS -->
<form method="GET" class="filters-bar">
    <input type="text" name="lieu" class="filter-input"
        value="<?= htmlspecialchars($lieu) ?>" placeholder="🔍 Ville, lieu...">
    <select name="type_contrat" class="filter-select">
        <option value="">Tous les contrats</option>
        <?php foreach ($typesContrat as $tc): ?>
            <option value="<?= $tc ?>" <?= $type_contrat === $tc ? 'selected' : '' ?>><?= $tc ?></option>
        <?php endforeach; ?>
    </select>
    <select name="tri" class="filter-select">
        <option value="">Trier par défaut</option>
        <option value="salaire_asc"  <?= $tri==='salaire_asc'  ? 'selected':'' ?>>Salaire ↑</option>
        <option value="salaire_desc" <?= $tri==='salaire_desc' ? 'selected':'' ?>>Salaire ↓</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
    <?php if ($lieu || $type_contrat || $tri): ?>
        <a href="offres.php" class="btn btn-ghost btn-sm">Réinitialiser</a>
    <?php endif; ?>
</form>

<?php if (empty($offres)): ?>
    <div class="empty-state">
        <p>Aucune offre ne correspond à votre recherche.</p>
        <a href="offres.php" class="btn btn-ghost btn-sm">Voir toutes les offres</a>
    </div>
<?php else: ?>
    <div class="offres-grid">
        <?php foreach ($offres as $o):
            $applied = isset($mesCandidatures[$o['id']]);
            $slug = strtolower(str_replace(['é','è','ê','î','É'], ['e','e','e','i','e'], $o['type_contrat']));
        ?>
            <div class="offre-card <?= $applied ? 'offre-card--applied' : '' ?>">
                <div class="offre-card__header">
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                        <span class="badge badge--<?= $slug ?>"><?= htmlspecialchars($o['type_contrat']) ?></span>
                        <?php if ($applied): ?><span class="badge badge--applied">Candidaté ✓</span><?php endif; ?>
                    </div>
                </div>
                <h2 class="offre-card__titre">
                    <a href="voir_offre.php?id=<?= $o['id'] ?>"><?= htmlspecialchars($o['titre']) ?></a>
                </h2>
                <p class="offre-card__entreprise"><?= htmlspecialchars($o['nom_createur']) ?></p>
                <div class="offre-card__meta">
                    <?php if ($o['lieu']): ?><span class="meta-item">📍 <?= htmlspecialchars($o['lieu']) ?></span><?php endif; ?>
                    <?php if ($o['salaire']): ?><span class="meta-item">💶 <?= number_format($o['salaire'],0,',',' ') ?> €/mois</span><?php endif; ?>
                    <span class="meta-item">👥 <?= $o['nb_candidats'] ?> candidat<?= $o['nb_candidats']>1?'s':'' ?></span>
                </div>
                <p class="offre-card__desc">
                    <?= htmlspecialchars(mb_substr($o['description'], 0, 140)) ?><?= mb_strlen($o['description'])>140?'…':'' ?>
                </p>
                <div class="offre-card__actions">
                    <a href="voir_offre.php?id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">Voir</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_id'] == $o['id_createur']): ?>
                            <a href="modifier_offre.php?id=<?= $o['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="supprimer_offre.php?id=<?= $o['id'] ?>" class="btn btn-danger btn-sm"
                               data-confirm="Supprimer cette offre ?" data-danger="true">Supprimer</a>
                        <?php elseif (!$applied): ?>
                            <a href="postuler.php?id=<?= $o['id'] ?>" class="btn btn-primary btn-sm">Postuler</a>
                        <?php else: ?>
                            <a href="retirer_candidature.php?id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm"
                               data-confirm="Retirer votre candidature ?" data-danger="false">Retirer</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
