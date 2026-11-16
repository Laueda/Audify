<?php
session_start();
require_once "../config.php";


if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}


$nb_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$nb_artists = $conn->query("SELECT COUNT(*) as total FROM artists")->fetch_assoc()['total'];
$nb_newsletters = $conn->query("SELECT COUNT(*) as total FROM NEWSLETTER")->fetch_assoc()['total'];
$nb_posts_signales = $conn->query("SELECT COUNT(*) as total FROM POSTS WHERE signale = 1")->fetch_assoc()['total'];
$nb_commentaires_signales = $conn->query("SELECT COUNT(*) as total FROM COMMENTAIRES WHERE signale = 1")->fetch_assoc()['total'];
$nb_commandes = $conn->query("SELECT COUNT(*) as total FROM COMMANDE_PLAYLIST WHERE traitee = 0")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Dashboard Admin</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droits</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="export_pdf.php">Exporter utilisateurs en PDF</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<div class="stats">
    <div class="card">
        <h3>Utilisateurs</h3>
        <p><?php echo $nb_users; ?></p>
    </div>
    <div class="card">
        <h3>Artistes</h3>
        <p><?php echo $nb_artists; ?></p>
    </div>
    <div class="card">
        <h3>Newsletters envoyées</h3>
        <p><?php echo $nb_newsletters; ?></p>
    </div>
    <div class="card">
        <h3>Signalements en attente</h3>
        <p><?php echo $nb_posts_signales + $nb_commentaires_signales; ?></p>
        <a href="moderation.php" style="font-size:0.85rem;">Voir la modération →</a>
    </div>
    <div class="card">
    <h3>Commandes en attente</h3>
    <p><?php echo $nb_commandes; ?></p>
    <a href="commandes.php" style="font-size:0.85rem;">Voir les commandes →</a>
</div>
</div>

</body>
</html>