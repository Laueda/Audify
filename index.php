<?php require_once "header.php"; ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>h

<?php if (isset($_SESSION['loggedin'])): ?>

<h2 style="color:#f2ead8; margin-bottom:0.25rem;">Bonjour <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
<p style="color:#666; margin-bottom:1.5rem;">Activité récente</p>

<h5 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">Derniers posts</h5>
<?php
$posts = $conn->query("SELECT p.contenu, u.pseudo, p.date_creation FROM POSTS p JOIN users u ON p.user_id = u.id ORDER BY p.date_creation DESC LIMIT 3");
while ($p = $posts->fetch_assoc()):
?>
  <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:0.75rem 1rem; margin-bottom:0.75rem;">
    <strong style="color:#c8a415;"><?php echo htmlspecialchars($p['pseudo']); ?></strong>
    <small style="color:#666;"> — <?php echo date('d/m H:i', strtotime($p['date_creation'])); ?></small>
    <p style="margin:0.5rem 0 0; color:#f2ead8;"><?php echo nl2br(htmlspecialchars(mb_strimwidth($p['contenu'], 0, 120, '...'))); ?></p>
  </div>
<?php endwhile; ?>
<a href="feed.php" style="color:#c8a415; font-size:0.85rem;">Voir tout →</a>

<hr style="border-color:#333; margin:1.5rem 0;">

<h5 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">Prochains événements</h5>
<?php
$events = $conn->query("SELECT e.titre, e.lieu, e.date_evenement, u.pseudo FROM EVENEMENT e JOIN users u ON e.organisateur_id = u.id WHERE e.date_evenement >= CURDATE() ORDER BY e.date_evenement ASC LIMIT 3");
while ($ev = $events->fetch_assoc()):
?>
  <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:0.75rem 1rem; margin-bottom:0.75rem;">
    <strong style="color:#c8a415;"><?php echo htmlspecialchars($ev['titre']); ?></strong>
    <small style="color:#666;"> — <?php echo date('d/m/Y', strtotime($ev['date_evenement'])); ?></small>
    <?php if ($ev['lieu']): ?><br><small style="color:#999;"><?php echo htmlspecialchars($ev['lieu']); ?></small><?php endif; ?>
  </div>
<?php endwhile; ?>
<a href="events.php" style="color:#c8a415; font-size:0.85rem;">Voir tout →</a>

<hr style="border-color:#333; margin:1.5rem 0;">

<h5 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">Playlists récentes</h5>
<?php
$playlists = $conn->query("SELECT p.nom, u.pseudo, p.date_creation FROM PLAYLIST p JOIN users u ON p.user_id = u.id WHERE p.type = 'publique' ORDER BY p.date_creation DESC LIMIT 3");
while ($pl = $playlists->fetch_assoc()):
?>
  <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:0.75rem 1rem; margin-bottom:0.75rem;">
    <strong style="color:#c8a415;"><?php echo htmlspecialchars($pl['nom']); ?></strong>
    <small style="color:#666;"> par <?php echo htmlspecialchars($pl['pseudo']); ?></small>
  </div>
<?php endwhile; ?>
<a href="playlists.php" style="color:#c8a415; font-size:0.85rem;">Voir tout →</a>
+
<?php else: ?>

<div style="text-align:center; margin-top:5rem;">
    <h1 style="color:#c8a415; font-size:3rem; letter-spacing:4px; margin-bottom:1rem;">AUDIFY</h1>
    <p style="color:#999; font-size:1.1rem; margin-bottom:2rem;">Découvre, partage et échange autour de la musique.</p>
    <a href="register.php" style="color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.6rem 1.5rem; text-decoration:none; margin-right:0.75rem;">S'inscrire</a>
    <a href="login.php" style="color:#999; border:1px solid #555; border-radius:6px; padding:0.6rem 1.5rem; text-decoration:none;">Se connecter</a>
</div>

<?php endif; ?>

<?php require_once "footer.php"; ?>
