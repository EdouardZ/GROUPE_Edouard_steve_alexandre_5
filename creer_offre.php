<?php
$page_title = "Publier une offre";
require_once __DIR__ . '/connexion_bd.php';
if (!isset($_SESSION['user_id'])) { header('Location: connexion.php'); exit; }

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
    if ($salaire !== '' && (!is_numeric($salaire) || $salaire < 0)) $errors[] = 'Le salaire doit être un nombre positif.';

    if (empty($errors)) {
        $pdo->prepare(
            'INSERT INTO offres (titre, description, salaire, lieu, type_contrat, id_createur) VALUES (?,?,?,?,?,?)'
        )->execute([
            $titre, $description,
            $salaire !== '' ? (float)$salaire : null,
            $lieu, $type, $_SESSION['user_id']
        ]);
        header('Location: offres.php'); exit;
    }
}

include 'header.php';
?>

<a href="offres.php" class="back-link">← Retour aux offres</a>
<div class="page-header" style="margin-top:.75rem">
    <h1>Publier une nouvelle offre</h1>
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
            <input type="text" id="titre" name="titre"
                value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                placeholder="Ex : Développeur PHP — Alternance" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="type_contrat">Type de contrat *</label>
                <select id="type_contrat" name="type_contrat" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($typesContrat as $tc): ?>
                        <option value="<?= $tc ?>" <?= ($_POST['type_contrat'] ?? '') === $tc ? 'selected' : '' ?>><?= $tc ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="lieu">Lieu</label>
                <input type="text" id="lieu" name="lieu"
                    value="<?= htmlspecialchars($_POST['lieu'] ?? '') ?>"
                    placeholder="Ex : Bordeaux, Télétravail...">
            </div>
            <div class="form-group">
                <label for="salaire">Salaire mensuel brut (€)</label>
                <input type="number" id="salaire" name="salaire"
                    value="<?= htmlspecialchars($_POST['salaire'] ?? '') ?>"
                    placeholder="Ex : 2500" min="0" step="50">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" rows="9"
                placeholder="Missions, profil recherché, avantages..." required
            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <div class="form-actions">
            <a href="offres.php" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">Publier l'offre</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
