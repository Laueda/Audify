<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$succes  = "";
$erreur  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ambiance = trim($_POST['ambiance']);
    $genres   = trim($_POST['genres']);
    $longueur = $_POST['longueur'];

    if ($ambiance === "" || $genres === "" || $longueur === "") {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $conn->query("INSERT INTO COMMANDE_PLAYLIST (user_id, ambiance, genres, longueur) VALUES ($user_id, '$ambiance', '$genres', '$longueur')");
        $succes = "Ta demande a bien été envoyée ! Nous reviendrons vers toi très bientôt.";
    }
}

require_once "header.php";
?>

<h2 style="color:#f2ead8; margin-bottom:0.5rem;">Commander une playlist</h2>
<p style="color:#999; margin-bottom:1.5rem;">Décris tes préférences et notre équipe créera une playlist personnalisée pour toi.</p>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem; margin-bottom:1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem; margin-bottom:1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; max-width:600px;">
    <form method="POST">

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.35rem;">Ambiance souhaitée</label>
            <p style="color:#999; font-size:0.85rem; margin-bottom:0.5rem;">Décrivez en quelques phrases l'ambiance que vous souhaitez pour votre playlist. (Ex: quelque chose d'énergique pour faire du sport, ou de calme pour travailler...)</p>
            <textarea name="ambiance" rows="4" required
                      style="width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;"
                      placeholder="Je voudrais une playlist..."></textarea>
        </div>

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.35rem;">Genres musicaux</label>
            <p style="color:#999; font-size:0.85rem; margin-bottom:0.5rem;">Quels genres musicaux aimez-vous ? Citez-en autant que vous voulez.</p>
            <textarea name="genres" rows="3" required
                      style="width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;"
                      placeholder="Ex: Rock, Jazz, Soul, années 80..."></textarea>
        </div>

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; color:#c8a415; margin-bottom:0.35rem;">Longueur de la playlist</label>
            <p style="color:#999; font-size:0.85rem; margin-bottom:0.5rem;">Combien de titres souhaitez-vous dans votre playlist ?</p>
            <div style="display:flex; flex-direction:column; gap:0.6rem; margin-top:0.5rem;">
                <label style="color:#f2ead8; cursor:pointer;">
                    <input type="radio" name="longueur" value="courte" required style="accent-color:#c8a415;"> Courte (environ 10 titres)
                </label>
                <label style="color:#f2ead8; cursor:pointer;">
                    <input type="radio" name="longueur" value="moyenne" style="accent-color:#c8a415;"> Moyenne (environ 20 titres)
                </label>
                <label style="color:#f2ead8; cursor:pointer;">
                    <input type="radio" name="longueur" value="longue" style="accent-color:#c8a415;"> Longue (environ 30 titres)
                </label>
            </div>
        </div>

        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.6rem 1.5rem; cursor:pointer; font-size:1rem;">
            Commander
        </button>

    </form>
</div>

<?php require_once "footer.php"; ?>