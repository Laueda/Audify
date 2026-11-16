<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";

if (isset($_GET['supprimer'])) {
    $pid = $_GET['supprimer'];
    $conn->query("DELETE FROM POSTS WHERE id = $pid AND user_id = $user_id");
    header("Location: feed.php"); exit();
}

if (isset($_GET['like'])) {
    $pid = $_GET['like'];
    $check = $conn->query("SELECT id FROM post_likes WHERE post_id = $pid AND user_id = $user_id");
    $existe = $check->fetch_assoc();
    if ($existe) {
        $conn->query("DELETE FROM post_likes WHERE post_id = $pid AND user_id = $user_id");
    } else {
        $conn->query("INSERT INTO post_likes (post_id, user_id) VALUES ($pid, $user_id)");
    }
    header("Location: feed.php"); exit();
}

if (isset($_GET['signaler'])) {
    $pid = $_GET['signaler'];
    $conn->query("UPDATE POSTS SET signale = 1 WHERE id = $pid");
    header("Location: feed.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contenu = trim($_POST['contenu']);
    if ($contenu === "") {
        $erreur = "Le post ne peut pas être vide.";
    } else {
        $conn->query("INSERT INTO POSTS (user_id, contenu, date_creation) VALUES ($user_id, '$contenu', NOW())");
        header("Location: feed.php"); exit();
    }
}

$res_abonnements = $conn->query("SELECT abonne_a_id FROM ABONNEMENT WHERE abonne_id = $user_id");
$res_abonnements = $res_abonnements ? $res_abonnements->fetch_all(MYSQLI_ASSOC) : [];

$mes_abonnements = [];
foreach ($res_abonnements as $ligne) {
    $mes_abonnements[] = $ligne['abonne_a_id'];
}

$tous_les_posts = $conn->query("SELECT p.*, u.pseudo, (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS nb_likes FROM POSTS p JOIN users u ON p.user_id = u.id ORDER BY p.date_creation DESC LIMIT 100");
$tous_les_posts = $tous_les_posts ? $tous_les_posts->fetch_all(MYSQLI_ASSOC) : [];

$posts = [];
foreach ($tous_les_posts as $post) {
    if ($post['user_id'] == $user_id || in_array($post['user_id'], $mes_abonnements)) {
        $posts[] = $post;
    }
}

$res_likes = $conn->query("SELECT post_id FROM post_likes WHERE user_id = $user_id");
$res_likes = $res_likes ? $res_likes->fetch_all(MYSQLI_ASSOC) : [];

$mes_likes = [];
foreach ($res_likes as $ligne) {
    $mes_likes[] = $ligne['post_id'];
}

require_once "header.php";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Fil d'actualité</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>

<form method="POST" style="margin-bottom:1.5rem;">
    <textarea name="contenu" rows="3" placeholder="Partage quelque chose..."
              style="width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.6rem 0.75rem; outline:none; box-sizing:border-box;"></textarea>
    <div style="text-align:right; margin-top:0.5rem;">
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Publier
        </button>
    </div>
</form>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($posts)): ?>
    <p style="color:#666;">Aucun post pour l'instant. Abonne-toi à d'autres utilisateurs pour voir leurs publications !</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <strong style="color:#c8a415;"><?php echo htmlspecialchars($post['pseudo']); ?></strong>
            <small style="color:#666;"><?php echo date('d/m/Y H:i', strtotime($post['date_creation'])); ?></small>
        </div>
        <p style="color:#f2ead8; margin:0 0 0.75rem;"><?php echo htmlspecialchars($post['contenu']); ?></p>
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="feed.php?like=<?php echo $post['id']; ?>"
               style="color:<?php echo in_array($post['id'], $mes_likes) ? '#c8a415' : '#666'; ?>; text-decoration:none; font-size:0.9rem;">
                ♥ <?php echo in_array($post['id'], $mes_likes) ? 'J\'aime' : 'Aimer'; ?> (<?php echo $post['nb_likes']; ?>)
            </a>
            <a href="commentaires.php?id=<?php echo $post['id']; ?>"
               style="color:#666; text-decoration:none; font-size:0.9rem;">
                Commenter
            </a>
            <?php if ($post['user_id'] == $user_id): ?>
                <a href="feed.php?supprimer=<?php echo $post['id']; ?>"
                   onclick="return confirm('Supprimer ?')"
                   style="color:#e74c3c; text-decoration:none; font-size:0.9rem;">Supprimer</a>
            <?php endif; ?>
            <a href="feed.php?signaler=<?php echo $post['id']; ?>"
               onclick="return confirm('Signaler ce post ?')"
               style="color:#999; text-decoration:none; font-size:0.9rem;">Signaler</a>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>