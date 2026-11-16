<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if (isset($_GET['supprimer'])) {
    $fid = $_GET['supprimer'];
    $conn->query("DELETE FROM SUJET WHERE id = $fid AND user_id = $user_id");
    header("Location: forums.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $forum_id = $_POST['forum_id'];
    $titre    = trim($_POST['titre']);
    $contenu  = trim($_POST['message']);
    if ($titre === "" || $contenu === "") {
        $erreur = "Le titre et le message sont obligatoires.";
    } else {
        $conn->query("INSERT INTO SUJET (user_id, forum_id, titre, date_creation) VALUES ($user_id, $forum_id, '$titre', NOW())");
        $succes = "Sujet publié !";
    }
}

$res = $conn->query("SELECT * FROM FORUM ORDER BY nom ASC");
$forums = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

$filtre = isset($_GET['forum_id']) ? $_GET['forum_id'] : 0;
if ($filtre !== 0) {
    $res = $conn->query("SELECT s.*, u.pseudo, f.nom AS forum_nom FROM SUJET s JOIN users u ON s.user_id = u.id JOIN FORUM f ON s.forum_id = f.id WHERE s.forum_id = $filtre ORDER BY s.date_creation DESC");
} else {
    $res = $conn->query("SELECT s.*, u.pseudo, f.nom AS forum_nom FROM SUJET s JOIN users u ON s.user_id = u.id JOIN FORUM f ON s.forum_id = f.id ORDER BY s.date_creation DESC");
}
$sujets = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
$group_style = "margin-bottom:1rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Forums</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
    <form method="POST">
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Catégorie</label>
            <select name="forum_id" style="<?php echo $input_style; ?>">
                <?php foreach ($forums as $f): ?>
                    <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Titre *</label>
            <input type="text" name="titre" required style="<?php echo $input_style; ?>">
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Message *</label>
            <textarea name="message" rows="4" required style="<?php echo $input_style; ?> resize:vertical;"></textarea>
        </div>
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Publier
        </button>
    </form>
</div>

<div style="margin-bottom:1.25rem; display:flex; gap:0.5rem; flex-wrap:wrap; align-items:center;">
    <span style="color:#666; font-size:0.9rem;">Filtrer :</span>
    <a href="forums.php" style="color:#c8a415; text-decoration:none; font-size:0.9rem; border:1px solid #333; border-radius:4px; padding:0.2rem 0.6rem;">Tous</a>
    <?php foreach ($forums as $f): ?>
        <a href="forums.php?forum_id=<?php echo $f['id']; ?>"
           style="color:#c8a415; text-decoration:none; font-size:0.9rem; border:1px solid #333; border-radius:4px; padding:0.2rem 0.6rem;">
            <?php echo htmlspecialchars($f['nom']); ?>
        </a>
    <?php endforeach; ?>
</div>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($sujets)): ?>
    <p style="color:#666;">Aucun sujet.</p>
<?php else: ?>
    <?php foreach ($sujets as $s): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:0.75rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <span style="color:#c8a415; font-size:0.8rem; text-transform:uppercase; letter-spacing:1px;"><?php echo htmlspecialchars($s['forum_nom']); ?></span>
                <div style="color:#f2ead8; font-weight:600; margin-top:0.2rem;"><?php echo htmlspecialchars($s['titre']); ?></div>
                <small style="color:#666;">Par <?php echo htmlspecialchars($s['pseudo']); ?> — <?php echo date('d/m/Y H:i', strtotime($s['date_creation'])); ?></small>
            </div>
            <?php if ($s['user_id'] == $user_id): ?>
                <a href="forums.php?supprimer=<?php echo $s['id']; ?>"
                   onclick="return confirm('Supprimer ?')"
                   style="color:#e74c3c; text-decoration:none; font-size:0.85rem;">Supprimer</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>
