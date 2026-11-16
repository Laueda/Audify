<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$connectes = $conn->query("SELECT pseudo, nom, prenom, statut, role, derniere_activite FROM users WHERE derniere_activite >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) ORDER BY derniere_activite DESC");
$connectes = $connectes ? $connectes->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connectés - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Utilisateurs connectés</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droits</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<p><?php echo count($connectes); ?> utilisateur(s) connecté(s) en ce moment</p>

<table>
    <tr>
        <th>Pseudo</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Statut</th>
        <th>Rôle</th>
        <th>Dernière activité</th>
    </tr>
    <?php if (empty($connectes)): ?>
    <tr><td colspan="6">Aucun utilisateur connecté.</td></tr>
    <?php else: ?>
    <?php foreach ($connectes as $u): ?>
    <tr>
        <td><?php echo htmlspecialchars($u['pseudo']); ?></td>
        <td><?php echo htmlspecialchars($u['nom']); ?></td>
        <td><?php echo htmlspecialchars($u['prenom']); ?></td>
        <td><?php echo htmlspecialchars($u['statut']); ?></td>
        <td><?php echo htmlspecialchars($u['role']); ?></td>
        <td><?php echo date('d/m/Y H:i', strtotime($u['derniere_activite'])); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
</table>

</body>
</html>