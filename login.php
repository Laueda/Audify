<?php
require_once "config.php";

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["id"]       = $user['id'];
            $_SESSION["username"] = $user['pseudo'];
            $_SESSION["role"]     = $user['role'];
            $_SESSION["statut"]   = $user['statut'];
            header("Location: index.php");
            exit();
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Aucun compte trouvé avec cet email.";
    }
}

require_once "header.php";
?>

<div style="max-width:420px; margin:3rem auto;">
    <h2 style="color:#c8a415; margin-bottom:1.5rem; text-align:center;">Se connecter</h2>

    <?php if ($erreur): ?>
        <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
    <?php endif; ?>

    <form method="POST">
        <div style="margin-bottom:1rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.35rem;">Email</label>
            <input type="email" name="email" required
                   style="width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;">
        </div>
        <div style="margin-bottom:1.5rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.35rem;">Mot de passe</label>
            <input type="password" name="password" required
                   style="width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;">
        </div>
        <div style="text-align:center;">
            <button type="submit"
                    style="width:60%; background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.6rem; cursor:pointer; font-size:1rem;">
                Se connecter
            </button>
        </div>
    </form>

    <p style="text-align:center; margin-top:1.25rem; color:#999;">
        Pas de compte ? <a href="register.php" style="color:#c8a415;">S'inscrire</a>
    </p>
</div>

<?php require_once "footer.php"; ?>
