<?php
$page_title = "Modifier l'offre";
require_once __DIR__ . '/connexion_bd.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: offres.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM offres WHERE id = ? AND id_createur = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$offre = $stmt->fetch();
if (!$offre) { header('Location: offres.php'); exit; }

$errors = [];
$typesContrat = ['CDI', 'CDD', 'Freelance', 'Stage', 'Intérim'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = trim($_POST['titre']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $salaire     =      $_POST['salaire']      ?? '';
    $lieu        = trim($_POST['lieu']         ?? '');
    $type        =      $_POST['type_contrat'] ?? '';

    if (empty($titre))                                               $errors[] = 'Le titre est obligatoire.';
    if (empty($description))                                         $errors[] = 'La description est obligatoire.';
    if (empty($type) || !in_array($type, $typesContrat))             $errors[] = 'Type de contrat invalide.';
    if ($salaire !== '' && (!is_numeric($salaire) || $salaire < 0)) $errors[] = 'Salaire invalide.';

    if (empty($errors)) {
        $pdo->prepare(
            'UPDATE offres SET titre=?, description=?, salaire=?, lieu=?, type_contrat=? WHERE id=? AND id_createur=?'
        )->execute([
            $titre, $description,
            $salaire !== '' ? (float)$salaire : null,
            $lieu, $type, $id, $_SESSION['user_id']
        ]);
        header("Location: voir_offre.php?id=$id&updated=1"); exit;
    }
    $offre = array_merge($offre, $_POST);
}

include 'header.php';
?>

<a href="voir_offre.php?id=<?= $id ?>" class="back-link">← Retour à l'offre</a>
<div class="page-header" style="margin-top:.75rem">
    <h1>Modifier l'offre</h1>
</div>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" class="form">
        <div class="form-group">
            <label for="titre">Titre du poste *</label>
            <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($offre['titre']) ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="type_contrat">Type de contrat *</label>
                <select id="type_contrat" name="type_contrat" required>
                    <?php foreach ($typesContrat as $tc): ?>
                        <option value="<?= $tc ?>" <?= $offre['type_contrat'] === $tc ? 'selected' : '' ?>><?= $tc ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="lieu">Lieu</label>
                <input type="text" id="lieu" name="lieu" value="<?= htmlspecialchars($offre['lieu'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="salaire">Salaire mensuel brut (€)</label>
                <input type="number" id="salaire" name="salaire" value="<?= htmlspecialchars($offre['salaire'] ?? '') ?>" min="0" step="50">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" rows="9" required><?= htmlspecialchars($offre['description']) ?></textarea>
        </div>
        <div class="form-actions">
            <a href="voir_offre.php?id=<?= $id ?>" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
