<?php
$page_title = "Connexion";
require_once __DIR__ . '/connexion_bd.php';
if (isset($_SESSION['user_id'])) { header('Location: tableau_de_bord.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = 'Tous les champs sont obligatoires.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            header('Location: tableau_de_bord.php'); exit;
        } else {
            $errors[] = 'Email ou mot de passe incorrect.';
        }
    }
}

include 'header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Bon retour 👋</h1>
            <p>Connectez-vous pour accéder à votre espace</p>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="votre@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
        </form>

        <p class="auth-footer">
            Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
