<?php
session_start();
require_once "../config.php";
require_once "function.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

logAction($conn, $_SESSION["id"], "visite", "stats.php");

$result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS particuliers FROM users WHERE statut='particulier'");
$particuliers = $result->fetch_assoc()['particuliers'];

$result = $conn->query("SELECT COUNT(*) AS musiciens FROM users WHERE statut='musicien'");
$musiciens = $result->fetch_assoc()['musiciens'];

$result = $conn->query("SELECT COUNT(*) AS compositeurs FROM users WHERE statut='compositeur'");
$compositeurs = $result->fetch_assoc()['compositeurs'];

$result = $conn->query("SELECT COUNT(*) AS total FROM POSTS");
$nb_posts = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM PLAYLIST");
$nb_playlists = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM EVENEMENT");
$nb_evenements = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM ABONNEMENT");
$nb_abonnements = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM MESSAGE");
$nb_messages = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Statistiques</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droit</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="export_pdf.php">Exporter utilisateurs en PDF</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<h3>Utilisateurs</h3>
<div class="stats">
    <div class="card">
        <h3>Total utilisateurs</h3>
        <p><?php echo $total; ?></p>
    </div>
    <div class="card">
        <h3>Particuliers</h3>
        <p><?php echo $particuliers; ?></p>
    </div>
    <div class="card">
        <h3>Musiciens</h3>
        <p><?php echo $musiciens; ?></p>
    </div>
    <div class="card">
        <h3>Compositeurs</h3>
        <p><?php echo $compositeurs; ?></p>
    </div>
</div>

<h3>Activité du site</h3>
<div class="stats">
    <div class="card">
        <h3>Posts publiés</h3>
        <p><?php echo $nb_posts; ?></p>
    </div>
    <div class="card">
        <h3>Playlists créées</h3>
        <p><?php echo $nb_playlists; ?></p>
    </div>
    <div class="card">
        <h3>Événements</h3>
        <p><?php echo $nb_evenements; ?></p>
    </div>
    <div class="card">
        <h3>Abonnements entre users</h3>
        <p><?php echo $nb_abonnements; ?></p>
    </div>
    <div class="card">
        <h3>Messages envoyés</h3>
        <p><?php echo $nb_messages; ?></p>
    </div>
</div>

</body>
</html>