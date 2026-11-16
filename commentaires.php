<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id  = $_SESSION['id'];
$post_id  = $_GET['id'] ?? 0;
$erreur   = "";
$succes   = "";

if ($post_id == 0) {
    header("Location: feed.php"); exit();
}

if (isset($_GET['supprimer'])) {
    $cid = $_GET['supprimer'];
    $conn->query("DELETE FROM COMMENTAIRES WHERE id = $cid AND user_id = $user_id");
    header("Location: commentaires.php?id=$post_id"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contenu = trim($_POST['contenu']);
    if ($contenu === "") {
        $erreur = "Le commentaire ne peut pas être vide.";
    } else {
        $conn->query("INSERT INTO COMMENTAIRES (user_id, post_id, contenu, date_creation) VALUES ($user_id, $post_id, '$contenu', NOW())");
        $succes = "Commentaire publié !";
    }
}

$res_post = $conn->query("SELECT p.*, u.pseudo FROM POSTS p JOIN users u ON p.user_id = u.id WHERE p.id = $post_id");
$post = $res_post ? $res_post->fetch_assoc() : null;

if (!$post) {
    header("Location: feed.php"); exit();
}

$res_comments = $conn->query("SELECT c.*, u.pseudo FROM COMMENTAIRES c JOIN users u ON c.user_id = u.id WHERE c.post_id = $post_id ORDER BY c.date_creation ASC");
$commentaires = $res_comments ? $res_comments->fetch_all(MYSQLI_ASSOC) : [];

require_once "header.php";
?>

<div style="margin-bottom:1.5rem;">
    <a href="feed.php" style="color:#999; text-decoration:none; font-size:0.9rem; border:1px solid #333; border-radius:6px; padding:0.35rem 0.9rem;">Retour</a>
</div>

<div style="background:#1e1e1e; border:1px solid #c8a415; border-radius:8px; padding:1rem 1.25rem; margin-bottom:1.5rem;">
    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
        <strong style="color:#c8a415;"><?php echo htmlspecialchars($post['pseudo']); ?></strong>
        <small style="color:#666;"><?php echo date('d/m/Y H:i', strtotime($post['date_creation'])); ?></small>
    </div>
    <p style="color:#f2ead8; margin:0;"><?php echo htmlspecialchars($post['contenu']); ?></p>
</div>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<form method="POST" style="margin-bottom:1.5rem; display:flex; gap:0.5rem;">
    <input type="text" name="contenu" placeholder="Écrire un commentaire..."
           style="flex:1; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none;">
    <button type="submit"
            style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.1rem; cursor:pointer;">
        Envoyer
    </button>
</form>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($commentaires)): ?>
    <p style="color:#666;">Aucun commentaire pour l'instant.</p>
<?php else: ?>
    <?php foreach ($commentaires as $c): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:0.75rem 1rem; margin-bottom:0.75rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.35rem;">
            <strong style="color:#c8a415; font-size:0.9rem;"><?php echo htmlspecialchars($c['pseudo']); ?></strong>
            <div style="display:flex; align-items:center; gap:1rem;">
                <small style="color:#555;"><?php echo date('d/m/Y H:i', strtotime($c['date_creation'])); ?></small>
                <?php if ($c['user_id'] == $user_id): ?>
                    <a href="commentaires.php?id=<?php echo $post_id; ?>&supprimer=<?php echo $c['id']; ?>"
                       onclick="return confirm('Supprimer ?')"
                       style="color:#e74c3c; text-decoration:none; font-size:0.8rem;">Supprimer</a>
                <?php endif; ?>
            </div>
        </div>
        <p style="color:#f2ead8; margin:0; font-size:0.9rem;"><?php echo htmlspecialchars($c['contenu']); ?></p>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>