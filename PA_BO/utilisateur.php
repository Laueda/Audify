<?php
session_start();
require_once "../config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$logs = $conn->query("SELECT l.*, u.pseudo FROM LOG l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.date DESC LIMIT 50");
$logs = $logs ? $logs->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logs utilisateurs - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h1>Audify - Back Office</h1>
<h2>Logs utilisateurs</h2>
<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="stats.php">Statistiques</a>
    <a href="droits.php">Familles de droit</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
    
</nav>
<table>
    <tr>
        <th>ID</th>
        <th>Utilisateur</th>
        <th>Action</th>
        <th>Page</th>
        <th>IP</th>
        <th>Date</th>
    </tr>
    <?php if (empty($logs)): ?>
    <tr><td colspan="6">Aucun log pour l'instant.</td></tr>
    <?php else: ?>
    <?php foreach ($logs as $log): ?>
    <tr>
        <td><?php echo $log['id']; ?></td>
        <td><?php echo $log['pseudo'] ?? 'Visiteur'; ?></td>
        <td><?php echo $log['action']; ?></td>
        <td><?php echo $log['page']; ?></td>
        <td><?php echo $log['ip']; ?></td>
        <td><?php echo $log['date']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
</table>
</body>
</html>