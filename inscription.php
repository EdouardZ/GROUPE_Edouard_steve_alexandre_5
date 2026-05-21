<?php
$page_title = "Inscription";
require_once __DIR__ . '/connexion_bd.php';
if (isset($_SESSION['user_id'])) { header('Location: tableau_de_bord.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom']              ?? '');
    $email    = trim($_POST['email']            ?? '');
    $password =      $_POST['password']         ?? '';
    $confirm  =      $_POST['password_confirm'] ?? '';
    $cv       = trim($_POST['cv_texte']         ?? '');

    if (empty($nom) || empty($email) || empty($password))
        $errors[] = 'Les champs nom, email et mot de passe sont obligatoires.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "L'adresse email n'est pas valide.";
    if (strlen($password) < 8)
        $errors[] = 'Le mot de passe doit faire au moins 8 caractères.';
    if ($password !== $confirm)
        $errors[] = 'Les mots de passe ne correspondent pas.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cette adresse email est déjà utilisée.';
        } else {
            // ⚠️ Jamais de mot de passe en clair — bcrypt obligatoire
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare(
                'INSERT INTO utilisateurs (nom, email, mot_de_passe, cv_texte) VALUES (?, ?, ?, ?)'
            )->execute([$nom, $email, $hash, $cv]);

            session_regenerate_id(true);
            $_SESSION['user_id']  = $pdo->lastInsertId();
            $_SESSION['user_nom'] = $nom;
            header('Location: tableau_de_bord.php'); exit;
        }
    }
}

include 'header.php';
?>

<div class="auth-container">
    <div class="auth-card auth-card--wide">
        <div class="auth-header">
            <h1>Créer un compte</h1>
            <p>Rejoignez la communauté pour postuler ou publier des offres</p>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom"
                        value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                        placeholder="Jean Dupont" required>
                </div>
                <div class="form-group">
                    <label for="email">Adresse email *</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="jean@exemple.com" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe * <span class="label-hint">(8 car. min.)</span></label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <div class="pw-strength"><div class="pw-strength__bar"></div></div>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirmer *</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" required>
                </div>
            </div>
            <div class="form-group">
                <label for="cv_texte">CV — Vos compétences</label>
                <textarea id="cv_texte" name="cv_texte" rows="4"
                    placeholder="Compétences, expériences, formations..."
                ><?= htmlspecialchars($_POST['cv_texte'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Créer mon compte</button>
        </form>

        <p class="auth-footer">
            Déjà un compte ? <a href="connexion.php">Se connecter</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
