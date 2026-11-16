<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

if (isset($_GET["supprimer_post"])) {
    $id = $_GET["supprimer_post"];
    $conn->query("DELETE FROM COMMENTAIRES WHERE post_id = $id");
    $conn->query("DELETE FROM POSTS WHERE id = $id");
    header("Location: moderation.php");
    exit();
}

if (isset($_GET["supprimer_commentaire"])) {
    $id = $_GET["supprimer_commentaire"];
    $conn->query("DELETE FROM COMMENTAIRES WHERE id = $id");
    header("Location: moderation.php");
    exit();
}

if (isset($_GET["valider_post"])) {
    $id = $_GET["valider_post"];
    $conn->query("UPDATE POSTS SET signale = 0 WHERE id = $id");
    header("Location: moderation.php");
    exit();
}

if (isset($_GET["valider_commentaire"])) {
    $id = $_GET["valider_commentaire"];
    $conn->query("UPDATE COMMENTAIRES SET signale = 0 WHERE id = $id");
    header("Location: moderation.php");
    exit();
}

$posts = $conn->query("SELECT p.*, u.pseudo FROM POSTS p JOIN users u ON p.user_id = u.id WHERE p.signale = 1");
$commentaires = $conn->query("SELECT c.*, u.pseudo FROM COMMENTAIRES c JOIN users u ON c.user_id = u.id WHERE c.signale = 1");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modération - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Modération du contenu</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droits</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="export_pdf.php">Exporter utilisateurs PDF</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<h3>Posts signalés</h3>
<table>
<tr>
    <th>ID</th>
    <th>Auteur</th>
    <th>Contenu</th>
    <th>Date</th>
    <th>Actions</th>
</tr>
<?php while($post = $posts->fetch_assoc()): ?>
<tr>
    <td><?php echo $post["id"]; ?></td>
    <td><?php echo htmlspecialchars($post["pseudo"]); ?></td>
    <td><?php echo htmlspecialchars($post["contenu"]); ?></td>
    <td><?php echo date('d/m/Y H:i', strtotime($post["date_creation"])); ?></td>
    <td>
        <a href="moderation.php?valider_post=<?php echo $post["id"]; ?>">Valider</a>
        |
        <a href="moderation.php?supprimer_post=<?php echo $post["id"]; ?>" onclick="return confirm('Supprimer ce post ?')">Supprimer</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<br>

<h3>Commentaires signalés</h3>
<table>
<tr>
    <th>ID</th>
    <th>Auteur</th>
    <th>Commentaire</th>
    <th>Date</th>
    <th>Actions</th>
</tr>
<?php while($com = $commentaires->fetch_assoc()): ?>
<tr>
    <td><?php echo $com["id"]; ?></td>
    <td><?php echo htmlspecialchars($com["pseudo"]); ?></td>
    <td><?php echo htmlspecialchars($com["contenu"]); ?></td>
    <td><?php echo date('d/m/Y H:i', strtotime($com["date_creation"])); ?></td>
    <td>
        <a href="moderation.php?valider_commentaire=<?php echo $com["id"]; ?>">Valider</a>
        |
        <a href="moderation.php?supprimer_commentaire=<?php echo $com["id"]; ?>" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>