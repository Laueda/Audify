<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$succes  = "";

if (isset($_GET['reinitialiser'])) {
    $conn->query("UPDATE user_preferences SET theme = 'dark', couleur_accent = '#c8a415' WHERE user_id = $user_id");
    header("Location: preferences.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $theme          = $_POST['theme'] === 'light' ? 'light' : 'dark';
    $couleur_accent = $_POST['couleur_accent'];

    $check = $conn->query("SELECT user_id FROM user_preferences WHERE user_id = $user_id");
    $existe = $check->fetch_assoc();

    if ($existe) {
        $conn->query("UPDATE user_preferences SET theme = '$theme', couleur_accent = '$couleur_accent' WHERE user_id = $user_id");
    } else {
        $conn->query("INSERT INTO user_preferences (user_id, theme, couleur_accent) VALUES ($user_id, '$theme', '$couleur_accent')");
    }

    $succes = "Préférences sauvegardées !";
}

$res = $conn->query("SELECT * FROM user_preferences WHERE user_id = $user_id");
$prefs = $res ? $res->fetch_assoc() : ['theme' => 'dark', 'couleur_accent' => '#c8a415'];

require_once "header.php";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Mes préférences</h2>

<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem; margin-bottom:1rem;"><?php echo $succes; ?></p>
<?php endif; ?>

<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; max-width:400px;">
    <form method="POST">

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.5rem;">Thème</label>
            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                <label style="color:#f2ead8; cursor:pointer;">
                    <input type="radio" name="theme" value="dark" style="accent-color:#c8a415;"
                        <?php echo ($prefs['theme'] === 'dark') ? 'checked' : ''; ?>>
                    Sombre
                </label>
                <label style="color:#f2ead8; cursor:pointer;">
                    <input type="radio" name="theme" value="light" style="accent-color:#c8a415;"
                        <?php echo ($prefs['theme'] === 'light') ? 'checked' : ''; ?>>
                    Clair
                </label>
            </div>
        </div>

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.5rem;">Couleur d'accent</label>
            <input type="color" name="couleur_accent" value="<?php echo htmlspecialchars($prefs['couleur_accent']); ?>"
                   style="width:60px; height:40px; border:none; background:none; cursor:pointer;">
        </div>

        <div style="display:flex; flex-direction:column; gap:0.5rem;">
            <button type="submit"
                    style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.6rem 1.5rem; cursor:pointer;">
                Enregistrer
            </button>
            <a href="preferences.php?reinitialiser=1"
               onclick="return confirm('Réinitialiser les préférences par défaut ?')"
               style="color:#e74c3c; text-decoration:none; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1.5rem; display:inline-block; text-align:center;">
                Réinitialiser
            </a>
        </div>

    </form>
</div>

<?php require_once "footer.php"; ?>