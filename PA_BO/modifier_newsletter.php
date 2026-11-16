<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"];


$nl = $conn->query("SELECT * FROM NEWSLETTER WHERE id = $id")->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST["titre"];
    $contenu = $_POST["contenu"];
    $statut = $_POST["statut"];

    $conn->query("UPDATE NEWSLETTER SET titre='$titre', contenu='$contenu', statut='$statut' WHERE id=$id");
    header("Location: newsletter.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier newsletter - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Modifier une newsletter</h2>

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

<div class="card">
    <form method="POST">

        <label>Titre</label>
        <input type="text" name="titre" value="<?php echo $nl['titre']; ?>" required>

        <label>Contenu</label>
        <textarea name="contenu" rows="6" required><?php echo $nl['contenu']; ?></textarea>

        <label>Statut</label>
        <select name="statut">
            <option value="envoyee" <?php if($nl['statut'] == 'envoyee') echo 'selected'; ?>>Envoyée</option>
            <option value="brouillon" <?php if($nl['statut'] == 'brouillon') echo 'selected'; ?>>Brouillon</option>
        </select>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="newsletter.php">Annuler</a>

    </form>
</div>

</body>
</html>