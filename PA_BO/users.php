<?php
session_start();
require_once "../config.php";


if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}


if (isset($_GET["supprimer"])) {
    $id = $_GET["supprimer"];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: users.php");
    exit();
}


$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion utilisateurs - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Gestion des utilisateurs</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droit</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<table>
    <tr>
        <th>ID</th>
        <th>Pseudo</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Statut</th>
        <th>Rôle</th>
        <th>Date inscription</th>
        <th>Actions</th>
    </tr>
    <?php while($user = $users->fetch_assoc()): ?>
    <tr>
        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['pseudo']; ?></td>
        <td><?php echo $user['nom']; ?></td>
        <td><?php echo $user['prenom']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td><?php echo $user['statut']; ?></td>
        <td><?php echo $user['role']; ?></td>
        <td><?php echo $user['date_inscription']; ?></td>
        <td>
            <a href="modifier_user.php?id=<?php echo $user['id']; ?>">Modifier</a>
            |
            <a href="users.php?supprimer=<?php echo $user['id']; ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>