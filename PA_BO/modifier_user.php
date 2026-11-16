<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"];


$user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST["pseudo"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $statut = $_POST["statut"];
    $role = $_POST["role"];

    $conn->query("UPDATE users SET pseudo='$pseudo', nom='$nom', prenom='$prenom', email='$email', statut='$statut', role='$role' WHERE id=$id");
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier utilisateur - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Modifier un utilisateur</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droit</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<div class="card">
    <form method="POST">

        <label>Pseudo</label>
        <input type="text" name="pseudo" value="<?php echo $user['pseudo']; ?>" required>

        <label>Nom</label>
        <input type="text" name="nom" value="<?php echo $user['nom']; ?>" required>

        <label>Prénom</label>
        <input type="text" name="prenom" value="<?php echo $user['prenom']; ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

        <label>Statut</label>
        <select name="statut">
            <option value="particulier" <?php if($user['statut'] == 'particulier') echo 'selected'; ?>>Particulier</option>
            <option value="musicien" <?php if($user['statut'] == 'musicien') echo 'selected'; ?>>Musicien</option>
            <option value="compositeur" <?php if($user['statut'] == 'compositeur') echo 'selected'; ?>>Compositeur</option>
        </select>

        <label>Rôle</label>
        <select name="role">
            <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>User</option>
            <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
        </select>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="users.php">Annuler</a>

    </form>
</div>

</body>
</html>