<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

$res = $conn->query("SELECT pseudo, nom, prenom, email, statut, date_inscription FROM users WHERE id = $user_id");
$user = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'profil') {
    $pseudo = trim($_POST['pseudo']);
    $statut = $_POST['statut'];
    $statuts_autorises = ['particulier', 'musicien', 'compositeur'];

    if ($pseudo === "") {
        $erreur = "Le pseudo ne peut pas être vide.";
    } elseif (!in_array($statut, $statuts_autorises)) {
        $erreur = "Statut invalide.";
    } else {
        $conn->query("UPDATE users SET pseudo = '$pseudo', statut = '$statut' WHERE id = $user_id");
        $_SESSION['username'] = $pseudo;
        $_SESSION['statut']   = $statut;
        $succes = "Profil mis à jour !";
 
        $res = $conn->query("SELECT pseudo, nom, prenom, email, statut, date_inscription FROM users WHERE id = $user_id");
        $user = $res->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'mdp') {
    $mdp_actuel  = $_POST['mdp_actuel'];
    $mdp_nouveau = $_POST['mdp_nouveau'];
    $mdp_confirm = $_POST['mdp_confirm'];

    $res_mdp = $conn->query("SELECT password FROM users WHERE id = $user_id");
    $resultat = $res_mdp->fetch_assoc();

    if (!password_verify($mdp_actuel, $resultat['password'])) {
        $erreur = "Mot de passe actuel incorrect.";
    } elseif (strlen($mdp_nouveau) < 8) {
        $erreur = "Le nouveau mot de passe doit faire au moins 8 caractères.";
    } elseif ($mdp_nouveau !== $mdp_confirm) {
        $erreur = "Les deux mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($mdp_nouveau, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hash' WHERE id = $user_id");
        $succes = "Mot de passe mis à jour !";
    }
}

require_once "header.php";

$input_style    = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$input_disabled = "width:100%; background:#1a1a1a; color:#666; border:1px solid #333; border-radius:6px; padding:0.5rem 0.75rem; box-sizing:border-box;";
$label_style    = "display:block; color:#c8a415; margin-bottom:0.35rem;";
$group_style    = "margin-bottom:1rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Mon profil</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<div style="max-width:560px; margin:0 auto;">
    <form method="POST">
        <input type="hidden" name="action" value="profil">
        <div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem;">
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Nom</label>
                <input type="text" value="<?php echo htmlspecialchars($user['nom']); ?>" disabled style="<?php echo $input_disabled; ?>">
            </div>
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Prénom</label>
                <input type="text" value="<?php echo htmlspecialchars($user['prenom']); ?>" disabled style="<?php echo $input_disabled; ?>">
            </div>
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="<?php echo $input_disabled; ?>">
            </div>
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Membre depuis</label>
                <input type="text" value="<?php echo isset($user['date_inscription']) ? date('d/m/Y', strtotime($user['date_inscription'])) : '-'; ?>" disabled style="<?php echo $input_disabled; ?>">
            </div>
            <hr style="border-color:#333; margin:1.25rem 0;">
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Pseudo</label>
                <input type="text" name="pseudo" required value="<?php echo htmlspecialchars($user['pseudo']); ?>" style="<?php echo $input_style; ?>">
            </div>
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Statut</label>
                <select name="statut" style="<?php echo $input_style; ?>">
                    <option value="particulier" <?php echo $user['statut'] === 'particulier' ? 'selected' : ''; ?>>Particulier</option>
                    <option value="musicien"    <?php echo $user['statut'] === 'musicien'    ? 'selected' : ''; ?>>Musicien</option>
                    <option value="compositeur" <?php echo $user['statut'] === 'compositeur' ? 'selected' : ''; ?>>Compositeur</option>
                </select>
            </div>
            <div style="text-align:center; margin-top:1.25rem;">
                <button type="submit"
                        style="width:60%; background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.6rem; cursor:pointer; font-size:1rem;">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>
</div>

<div style="max-width:560px; margin:2rem auto 0;">
    <div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem;">
        <h3 style="color:#c8a415; margin:0 0 1.25rem;">Changer le mot de passe</h3>
        <form method="POST">
            <input type="hidden" name="action" value="mdp">
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Mot de passe actuel</label>
                <input type="password" name="mdp_actuel" required style="<?php echo $input_style; ?>">
            </div>
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Nouveau mot de passe</label>
                <input type="password" name="mdp_nouveau" required minlength="8" style="<?php echo $input_style; ?>">
            </div>
            <div style="<?php echo $group_style; ?>">
                <label style="<?php echo $label_style; ?>">Confirmer le nouveau mot de passe</label>
                <input type="password" name="mdp_confirm" required minlength="8" style="<?php echo $input_style; ?>">
            </div>
            <div style="text-align:center; margin-top:1.25rem;">
                <button type="submit"
                        style="width:60%; background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.6rem; cursor:pointer; font-size:1rem;">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once "footer.php"; ?>