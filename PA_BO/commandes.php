<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

if (isset($_GET['traiter'])) {
    $id = $_GET['traiter'];
    $conn->query("UPDATE COMMANDE_PLAYLIST SET traitee = 1 WHERE id = $id");
    header("Location: commandes.php");
    exit();
}

$commandes = $conn->query("SELECT c.*, u.pseudo, u.email FROM COMMANDE_PLAYLIST c JOIN users u ON c.user_id = u.id ORDER BY c.date_commande DESC");
$commandes = $commandes ? $commandes->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes playlists - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Commandes de playlists</h2>

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
    <a href="export_pdf.php">Exporter utilisateurs en PDF</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<table>
    <tr>
        <th>ID</th>
        <th>Utilisateur</th>
        <th>Email</th>
        <th>Ambiance</th>
        <th>Genres</th>
        <th>Longueur</th>
        <th>Date</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>
    <?php if (empty($commandes)): ?>
    <tr><td colspan="9">Aucune commande pour l'instant.</td></tr>
    <?php else: ?>
    <?php foreach ($commandes as $commande): ?>
    <tr>
        <td><?php echo $commande['id']; ?></td>
        <td><?php echo htmlspecialchars($commande['pseudo']); ?></td>
        <td><?php echo htmlspecialchars($commande['email']); ?></td>
        <td><?php echo htmlspecialchars($commande['ambiance']); ?></td>
        <td><?php echo htmlspecialchars($commande['genres']); ?></td>
        <td><?php echo htmlspecialchars($commande['longueur']); ?></td>
        <td><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></td>
        <td><?php echo $commande['traitee'] ? 'Traitée' : 'En attente'; ?></td>
        <td>
            <?php if (!$commande['traitee']): ?>
                <a href="commandes.php?traiter=<?php echo $commande['id']; ?>"
                   onclick="return confirm('Marquer comme traitée ?')"
                   style="color:#2ecc71; text-decoration:none;">Marquer traitée</a>
            <?php else: ?>
                <span style="color:#555;">Terminé</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
</table>

</body>
</html>